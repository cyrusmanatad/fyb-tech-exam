<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
    $rules = [
        'category_id' => 'integer|exists:categories,id',

        // base SKU (product level)
        'base_sku' => 'required|string|unique:products,base_sku',

        'title' => 'required|string',
        'description' => 'nullable|string',
        'uom' => 'required|string',

        'price' => 'required|numeric|min:0',
        'sale_price' => 'required|numeric|min:0',

        'status' => 'required|in:published,out-of-stock,inactive,draft',
        'stock' => 'required|numeric|min:0',

        'slug' => 'nullable|string',
        'currency' => 'nullable|string|size:3',

        'options' => 'nullable|array',

        // variants required on create
        'variants' => 'required|array|min:1',

        'variants.*.sku' => [
            'required',
            'string',
            'distinct', // no duplicates in request
            'unique:product_variants,sku', // unique in DB
        ],

        'variants.*.price' => 'required|numeric|min:0',
        'variants.*.sale_price' => 'required|numeric|min:0',
        'variants.*.stock' => 'required|numeric|min:0',
        'variants.*.reserved_quantity' => 'nullable|numeric|min:0',
        'variants.*.attributes' => 'required|array',
    ];

    return $rules;
    }
}
