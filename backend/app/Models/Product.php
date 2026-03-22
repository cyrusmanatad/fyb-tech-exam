<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'category_id',
        'base_sku',
        'title',
        'description',
        'slug',
        'status',
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

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getHumanizeDatetimeAttribute(){
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }
}
