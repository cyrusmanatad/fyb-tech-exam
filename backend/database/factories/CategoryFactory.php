<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'name' => $this->faker->words(2, true),
        'slug' => $this->faker->slug(),
        'path' => $this->faker->slug(),
        'level' => mt_rand(1,5),
        'is_active' => 1,
        'sort_order' => mt_rand(1,20),
        ];
    }
}
