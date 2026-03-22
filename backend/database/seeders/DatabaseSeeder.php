<?php

namespace Database\Seeders;

use App\Models\Forum;
use App\Models\ForumsComment;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            CategorySeeder::class,
        ]);

        // Create 20 products with 5 reviews each
        $products = Product::factory()
            ->count(40)
            ->hasReviews(5)
            ->create();

        // Generate variants and inventories
        foreach ($products as $product) {
            $variantCount = mt_rand(1, 2); // 1-2 variants per product
            $usedCombinations = [];        // Track existing combinations per product

            for ($i = 0; $i < $variantCount; $i++) {
                $color = fake()->randomElement(['Black', 'White']);
                $size = fake()->randomElement(['30-inch', '50-inch', '60-inch']);
                $includeSize = fake()->boolean(); // Some variants only Color

                $attributes = ['Color' => $color];
                if ($includeSize) {
                    $attributes['Size'] = $size;
                }

                ksort($attributes); // Normalize JSON keys
                $attributesJson = json_encode($attributes);

                // Skip duplicate combinations
                if (in_array($attributesJson, $usedCombinations)) {
                    continue;
                }
                $usedCombinations[] = $attributesJson;

                // Generate unique SKU
                $sku = 'SKU-' . strtoupper($color[0])
                    . ($includeSize ? '-' . strtoupper(str_replace('-', '', $size)) : '')
                    . '-' . fake()->unique()->numberBetween(1000, 9999);

                // Create variant if it doesn't exist
                $variant = ProductVariant::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'attributes' => $attributesJson,
                    ],
                    [
                        'sku' => $sku,
                        'uom' => fake()->randomElement(['pcs', 'box', 'kg', 'lt']),
                        'price' => fake()->randomFloat(2, 500, 1000),
                        'sale_price' => fake()->randomFloat(2, 400, 500),
                        'currency' => fake()->currencyCode(),
                    ]
                );

                Inventory::factory()->create([
                    'variant_id' => $variant->id,
                ]);
            }
        }

        // Forums + Comments
        Forum::factory(5)->hasComments(mt_rand(1,5))->create();

        // ForumsComment::factory()->count(10)->create();
    }
}
