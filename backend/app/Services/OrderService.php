<?php

namespace App\Services;

use App\DTOs\OrderData;
use App\DTOs\OrderItemData;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create order with items inside a transaction
     */
    public function create(OrderData $data): Order
    {
        return DB::transaction(function () use ($data) {
            // 1. Validate stock availability before anything
            $this->validateStock($data->items);

            // 2. Resolve variants with their products
            $variantIds = $data->items->pluck('variant_id')->toArray();
            $variants   = ProductVariant::with('product', 'inventory')
                            ->whereIn('id', $variantIds)
                            ->get()
                            ->keyBy('id'); // key by id for easy lookup

            // 3. Calculate totals
            $subtotal = $this->calculateSubtotal($data->items, $variants);
            $total    = $subtotal
                        - $data->discount
                        + $data->tax
                        + $data->shipping_fee;

            // 4. Create Order
            $order = Order::create([
                'order_number'   => $this->generateOrderNumber(),
                'user_id'        => $data->user_id,
                'subtotal'       => $subtotal,
                'tax'            => $data->tax,
                'discount'       => $data->discount,
                'shipping_fee'   => $data->shipping_fee,
                'total'          => max(0, $total), // prevent negative total
                'currency'       => $data->currency,
                'status'         => OrderStatus::PENDING,
                'payment_status' => PaymentStatus::UNPAID,
                'payment_method' => $data->payment_method,
                'shipping_method'=> $data->shipping_method,
                'notes'          => $data->notes,
            ]);

            // 5. Create Order Items + deduct inventory
            foreach ($data->items as $item) {
                $variant = $variants->get($item->variant_id);

                $this->createOrderItem($order, $item, $variant);
                $this->deductInventory($variant, $item->quantity);
            }

            return $order->fresh(['items.variant.product']);
        });
    }

    /**
     * Validate stock availability for all items before processing
     */
    private function validateStock(iterable $items): void
    {
        $errors = [];

        foreach ($items as $item) {
            $variant = ProductVariant::with('inventory')->find($item->variant_id);

            if (!$variant || !$variant->is_active) {
                $errors["items.{$item->variant_id}"] = [
                    "Variant {$item->variant_id} is not available."
                ];
                continue;
            }

            $available = ($variant->inventory?->stock_quantity ?? 0)
                       - ($variant->inventory?->reserved_quantity ?? 0);

            if ($item->quantity > $available) {
                $errors["items.{$item->variant_id}"] = [
                    "Insufficient stock for SKU {$variant->sku}. 
                     Available: {$available}, Requested: {$item->quantity}"
                ];
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Create a single order item with price snapshot
     */
    private function createOrderItem(
        Order          $order,
        OrderItemData  $item,
        ProductVariant $variant
    ): void {
        $unitPrice  = (float) $variant->price;
        $salePrice  = (float) ($variant->sale_price ?? $variant->price);
        $finalPrice = $item->price_type === 'sale' && $variant->sale_price
                        ? $salePrice
                        : $unitPrice;

        $order->items()->create([
            'variant_id'   => $variant->id,

            // Snapshot — preserve at time of order
            'sku'          => $variant->sku,
            'product_name' => $variant->product->title ?? $variant->sku,
            'variant_name' => $this->resolveVariantName($variant),
            'uom'          => $variant->uom,
            'unit_price'   => $unitPrice,
            'sale_price'   => $salePrice,
            'final_price'  => $finalPrice,
            'price_type'   => $item->price_type,
            'attributes'   => $variant->attributes,

            'quantity'     => $item->quantity,
            'subtotal'     => round($finalPrice * $item->quantity, 2),
        ]);
    }

    /**
     * Deduct stock from inventory
     */
    private function deductInventory(ProductVariant $variant, int $quantity): void
    {
        $variant->inventory()->decrement('stock_quantity', $quantity);
    }

    /**
     * Calculate order subtotal from all items
     */
    private function calculateSubtotal(iterable $items, $variants): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $variant    = $variants->get($item->variant_id);
            $unitPrice  = (float) $variant->price;
            $salePrice  = (float) ($variant->sale_price ?? $variant->price);
            $finalPrice = $item->price_type === 'sale' && $variant->sale_price
                            ? $salePrice
                            : $unitPrice;

            $subtotal += $finalPrice * $item->quantity;
        }

        return round($subtotal, 2);
    }

    /**
     * Resolve human readable variant name from attributes
     * e.g. Color: Black, Size: 30-inch → Black / 30-inch
     */
    private function resolveVariantName(ProductVariant $variant): ?string
    {
        if (empty($variant->attributes)) return null;

        return collect($variant->attributes)
            ->values()
            ->implode(' / '); // Black / 30-inch
    }

    /**
     * Generate unique order number
     * e.g. ORD-2026-00001
     */
    private function generateOrderNumber(): string
    {
        $year   = now()->format('Y');
        $prefix = "ORD-{$year}-";

        $latest = Order::query()->where('order_number', 'like', "{$prefix}%")
            ->orderByDesc('order_number')
            ->value('order_number');

        $next = $latest
            ? (int) str($latest)->afterLast('-')->toInteger() + 1
            : 1;

        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}