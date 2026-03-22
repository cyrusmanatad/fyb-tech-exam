<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        // $variantId = $product->variants()->value('id');
        
        $rules = [
            'category_id' => 'integer|exists:categories,id',
            'base_sku' => [
                'required',
                'string',
                Rule::unique('products', 'base_sku')->ignore($product?->id),
            ],
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
            'variants.*.sku' => [
                'required',
                'string',
                'distinct',
            ],
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|numeric|min:0',
            'variants.*.reserved_quantity' => 'nullable|numeric|min:0',
            'variants.*.attributes' => 'required|array',
        ];

        // handle unique SKU per variant (update-safe)
        // foreach ($this->input('variants', []) as $index => $variant) {
        //     $rules["variants.$index.sku"][] =
        //         Rule::unique('product_variants', 'sku')
        //             ->ignore($variant['id'] ?? null);
        // }

        return $rules;
    }
}
