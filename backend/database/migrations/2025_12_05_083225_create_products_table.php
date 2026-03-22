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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('base_sku')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('slug')->unique();
            $table->enum('status', ['published','out-of-stock','inactive','draft'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('values');
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('uom');
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('currency', 10)->default('PHP');
            $table->boolean('is_active')->default(true);
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->string('attributes_hash')->storedAs('md5(attributes)');
            $table->unique(['product_id', 'attributes_hash']);
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')
                  ->constrained('product_variants')
                  ->cascadeOnDelete();
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->timestamps();

            $table->unique('variant_id');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->text('url');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // VARIANT ATTRIBUTE VALUES (pivot-style)
        // Schema::create('variant_attribute_values', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('variant_id')
        //           ->constrained('product_variants')
        //           ->cascadeOnDelete();
        //     $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();

        //     $table->unique(['variant_id', 'attribute_value_id']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
