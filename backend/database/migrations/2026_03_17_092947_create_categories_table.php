<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // GLOBAL CATEGORIES
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            // hierarchy
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->cascadeOnDelete();

            // optional optimization
            $table->string('path')->nullable(); // e.g. electronics/phones
            $table->integer('level')->default(0);

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        // VENDOR CATEGORY MAPPING (important for multi-vendor)
        Schema::create('vendor_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->unique(['vendor_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_categories');
        Schema::dropIfExists('categories');
    }
};
