<?php

namespace App\Observers;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductVariantObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        if (!$product->isForceDeleting()) {
            $product->withoutEvents(function () use ($product) {
                $product->update(['status' => ProductStatus::INACTIVE]); // string value
                
                $product->variants->each(
                    fn($variant) => $variant->update(['is_active' => false])
                );
            });
        }
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $product->withoutEvents(function () use ($product) {
            $product->update(['status' => ProductStatus::DRAFT]); // string value

            $product->variants->each(
                fn($variant) => $variant->update(['is_active' => true])
            );
        });
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        Log::info("Product permanently deleted", ['product_id' => $product->id]);
    }
}
