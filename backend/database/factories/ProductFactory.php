<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
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
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'base_sku' => $this->faker->unique()->bothify('FF-##'),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(2, true),
            'slug' => $this->faker->slug(),
            'status' => ProductStatus::cases()[array_rand(ProductStatus::cases())]->value,
        ];
    }

    public function hasVariants($count = 1)
    {
        return $this->has(ProductVariant::factory()->count($count), 'variants');
    }
}