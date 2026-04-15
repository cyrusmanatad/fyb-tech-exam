<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{

    protected $fillable = [
        'order_id',
        'variant_id',
        'sku',
        'product_name',
        'variant_name',
        'uom',
        'unit_price',
        'sale_price',
        'final_price',
        'price_type',
        'quantity',
        'subtotal',
        'attributes',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
