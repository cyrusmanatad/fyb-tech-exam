<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'desc' => $this->faker->sentence(),
            'desc_long' => $this->faker->paragraph(2, true),
            'uom' => $this->faker->randomElement(['pcs', 'box', 'kg', 'lt']),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sale_price' => $this->faker->randomFloat(2, 10, 900),
            'currency' => $this->faker->currencyCode()
        ];
    }
}