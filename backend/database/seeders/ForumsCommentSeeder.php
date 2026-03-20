<?php

namespace Database\Seeders;

use App\Models\ForumsComment;
use Illuminate\Database\Seeder;

class ForumsCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ForumsComment::factory()->count(10)->create();
    }
}
