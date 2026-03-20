<?php

namespace Database\Seeders;

use App\Models\Forum;
use App\Models\ForumsComment;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

        Product::factory()->hasReviews(5)->count(20)->create();

        ProductVariant::factory()->count(40)->create();

        Inventory::factory()->count(40)->create();

        ProductReview::factory(20)->create();

        Forum::factory(5)->hasComments(mt_rand(1,5))->create();

        ForumsComment::factory()->count(10)->create();
    }
}
