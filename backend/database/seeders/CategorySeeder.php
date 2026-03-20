<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Electronics', 'Fashion', 'Home & Living', 'Beauty', 'Sports', 'Gadgets'];

        $mapCategories = array_map(function ($category, $index) {
            $ctr = $index + 1;

            return [
                'name' => $category,
                'slug' => Str::slug($category),
                'path' => Str::slug($category),
                'level' => $ctr,
                'is_active' => 1,
                'sort_order' => $ctr,
            ];
        }, $categories, array_keys($categories));

        Category::insert($mapCategories);
    }
}
