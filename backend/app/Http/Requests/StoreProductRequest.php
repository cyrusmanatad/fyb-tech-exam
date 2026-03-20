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
            'sku' => 'required|string|unique:product_variants,sku',
            'desc' => 'required|string',
            'desc_long' => 'nullable|string',
            'uom' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'status' => 'required|in:published,out-of-stock,inactive,draft',
            'stock' => 'required|numeric|min:0',
            'slug' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
        ];

        return $rules;
    }
}
