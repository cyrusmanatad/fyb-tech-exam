<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Resources\CatalogProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Published products for the public shop.
     */
    public function products(Request $request)
    {
        $query = Product::query()
            ->select(['id', 'category_id', 'base_sku', 'title', 'description', 'slug', 'status', 'created_at'])
            ->with([
                'variants:id,product_id,sku,uom,price,sale_price,currency,attributes',
                'variants.inventory:id,variant_id,stock_quantity,reserved_quantity',
                'category:id,name',
            ])
            ->where('status', ProductStatus::PUBLISHED->value)
            ->orderByDesc('created_at');

        if ($request->filled('category')) {
            $categories = $request->category;
            $query->whereHas('category', function ($q) use ($categories) {
                $q->whereIn('id', (array) $categories);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('variants', function ($q2) use ($search) {
                        $q2->where('sku', 'like', "%{$search}%")
                            ->orWhere('uom', 'like', "%{$search}%");
                    });
            });
        }

        return CatalogProductResource::collection($query->paginate(10));
    }

    /**
     * Categories that currently have a published product.
     */
    public function categories()
    {
        return Category::query()
            ->select(['id', 'name'])
            ->whereHas('products', function ($query) {
                $query->where('status', ProductStatus::PUBLISHED->value);
            })
            ->orderBy('name')
            ->get();
    }
}
