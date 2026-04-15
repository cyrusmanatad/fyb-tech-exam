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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Totals
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 10)->default('PHP');

            // Status
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Shipping
            $table->string('shipping_method')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            // Snapshot fields — store at time of purchase
            // in case variant price changes later
            $table->string('sku');
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('uom');
            $table->decimal('unit_price', 12, 2);     // original price at time of order
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('final_price', 12, 2);    // actual price charged
            $table->string('price_type');             // 'sale' or 'original'
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);       // final_price * quantity
            $table->json('attributes')->nullable();   // snapshot of variant attributes

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
