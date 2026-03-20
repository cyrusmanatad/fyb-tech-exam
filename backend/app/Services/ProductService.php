<?php
// app/Services/OrderService.php

namespace App\Services;

use App\DTOs\ProductData;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getAll()
    {
        return Product::all();
    }
    /**
     * Create a new order with items
     */
    public function create(ProductData $data): Product
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Product
            $product = Product::create([
                'user_id'     => $data->user_id,
                'category_id' => $data->category_id,
                'slug'        => $data->slug,
                'status'      => $data->status,
            ]);

            // 2. Create Variant
            $variant = $product->variants()->create([
                'sku'        => $data->sku,
                'desc'       => $data->desc,
                'desc_long'  => $data->desc_long ?? null,
                'uom'        => $data->uom,
                'price'      => $data->sell_price,
                'sale_price' => $data->price,
                'currency'   => $data->currency ?? 'USD',
            ]);

            // 3. Create Inventory for that Variant
            $variant->inventory()->create([
                'stock_quantity'    => $data->stock_quantity ?? 0,
                'reserved_quantity' => 0,
            ]);

            return $product->fresh(['variants.inventory']);
        });
    }

    public function update(Product $product, ProductData $data): bool
    {
        return DB::transaction(function () use ($product, $data) {
            // 1. Update Product
            $product->update([
                'category_id' => $data->category_id,
                'slug'        => $data->slug,
                'status'      => $data->status,
            ]);

            // 2. Update or Create Variant
            $variant = $product->variants()->first();

            if ($variant) {
                $variant->update([
                    'sku'        => $data->sku,
                    'desc'       => $data->desc,
                    'desc_long'  => $data->desc_long ?? null,
                    'uom'        => $data->uom,
                    'price'      => $data->sell_price,
                    'sale_price' => $data->price,
                    'currency'   => $data->currency ?? $variant->currency,
                ]);

                // 3. Update or Create Inventory
                $variant->inventory()->updateOrCreate(
                    ['variant_id' => $variant->id],  // find by
                    ['stock_quantity' => $data->stock] // update with
                );
            }

            return true;
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Cascade delete variants and inventory
            // (or rely on onDelete('cascade') in migrations)
            foreach ($product->variants as $variant) {
                $variant->inventory()->delete();
                $variant->delete();
            }

            return $product->delete();
        });
    }
}