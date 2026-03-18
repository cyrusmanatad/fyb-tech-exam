<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate stock across all variants
        $totalStock = $this->variants->sum(function ($variant) {
            return optional($variant->inventory)->stock_quantity ?? 0;
        });

        $totalReserved = $this->variants->sum(function ($variant) {
            return optional($variant->inventory)->reserved_quantity ?? 0;
        });

        $availableStock = $totalStock - $totalReserved;

        // Get price from first variant
        $firstVariant = $this->variants->first();

        return [
            'id'        => $this->id,
            'sku'       => $firstVariant?->sku ?? "",
            'desc'      => $firstVariant?->desc ?? "",
            'category_id' => $this->category_id,
            'category'  => $this->category?->name ?? 'Uncategorized',
            'price'     => number_format($firstVariant?->price, 2) ?? '0',
            'sell_price' => number_format($firstVariant?->sale_price, 2) ?? '0',
            'stock'     => number_format($availableStock),
            'uom'       => $firstVariant?->uom ?? "",
            'status'    => $this->resolveStatus($availableStock),
            'createdAt' => $this->humanize_datetime,
        ];
    }

    private function resolveStatus(int $stock): string
    {
        return match(true) {
            $this->status === 'published' && $stock > 0 => 'Published',
            $this->status === 'published' && $stock <= 0 => 'Out Stock',
            $this->status === 'draft'                   => 'Draft List',
            default                                     => 'Inactive',
        };
    }
}
