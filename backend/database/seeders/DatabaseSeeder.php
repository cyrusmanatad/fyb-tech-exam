<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumsComment;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Pest\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Cyrus Manatad',
            'email' => 'cyrusmanatad@bentadoor.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password@1234'),
            'remember_token' => Str::random(10),
        ]);

        User::factory()->count(5)->create();

        Category::factory()->count(20)->create();

        Product::factory()->hasReviews(5)->count(20)->create();

        ProductVariant::factory()->count(40)->create();

        Inventory::factory()->count(40)->create();

        ProductReview::factory(20)->create();

        Forum::factory(5)->hasComments(mt_rand(1,5))->create();

        ForumsComment::factory()->count(10)->create();
    }
}
