<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        $variantId = $product->variants()->value('id');
        
        $rules = [
            'category_id' => 'numeric',
            'sku' => [
                'required',
                'string',
                Rule::unique('product_variants', 'sku')->ignore($variantId),
            ],
            'desc' => 'required|string',
            'desc_long' => 'string',
            'uom' => 'required|string',
            'price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'status' => 'required|string',
            'stock' => 'required|numeric',
            'slug' => 'string',
            'currency' => 'string',
        ];

        return $rules;
    }
}
