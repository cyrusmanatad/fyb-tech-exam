<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'sku_code',
        'sku_desc',
        'sku_desc_long',
        'sku_uom',
        'sku_price',
    ];

    protected $appends = ['humanize_datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getHumanizeDatetimeAttribute(){
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }
}
