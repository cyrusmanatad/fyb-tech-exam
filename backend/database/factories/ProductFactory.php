<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'sku_code' => $this->faker->unique()->word() . $this->faker->randomNumber(3),
            'sku_desc' => $this->faker->sentence(),
            'sku_uom' => $this->faker->randomElement(['Pcs', 'Box', 'Kg', 'Lt']),
            'sku_price' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}