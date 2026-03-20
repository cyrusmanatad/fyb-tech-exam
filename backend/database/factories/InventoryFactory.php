<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'variant_id' => ProductVariant::inRandomOrder()->first()->id ?? ProductVariant::factory(),
            'stock_quantity' => mt_rand(50,99999),
            'reserved_quantity' => mt_rand(50, 100),
            'low_stock_threshold' => mt_rand(50, 100),
        ];
    }
}