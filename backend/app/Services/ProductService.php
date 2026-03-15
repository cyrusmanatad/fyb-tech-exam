<?php
// app/Services/OrderService.php

namespace App\Services;

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
    public function create(array $validatedData): Product
    {
        return DB::transaction(function () use ($validatedData) {
            // Create the main order
            $product = Product::create([
                "user_id" => $validatedData['user_id'], 
                "sku_code" => $validatedData['sku_code'], 
                "sku_desc" => $validatedData['sku_desc'], 
                "sku_desc_long" => $validatedData['sku_desc_long'], 
                "sku_uom" => $validatedData['sku_uom'], 
                "sku_price" => $validatedData['sku_price'],
            ]);

            return $product->fresh(); // Return fresh instance with updated data
        });
    }

    public function update(Product $product, array $validatedData): bool
    {
        return DB::transaction(function () use ($product, $validatedData) {
            return $product->update([
                "user_id" => $validatedData['user_id'], 
                "sku_code" => $validatedData['sku_code'], 
                "sku_desc" => $validatedData['sku_desc'], 
                "sku_desc_long" => $validatedData['sku_desc_long'], 
                "sku_uom" => $validatedData['sku_uom'], 
                "sku_price" => $validatedData['sku_price'],
            ]);
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return $product->delete();
        });
    }
}