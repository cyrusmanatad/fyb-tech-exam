<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
                // Prevent duplicate variant in same order
                Rule::unique('order_items', 'variant_id')
                    ->where('order_id', $this->route('order')),
            ],
            'quantity'   => ['required', 'integer', 'min:1'],
            'price_type' => ['required', 'string', Rule::in(['sale', 'original'])],
        ];
    }

    public function messages(): array
    {
        return [
            'variant_id.required' => 'Variant is required.',
            'variant_id.exists'   => 'Selected variant does not exist.',
            'variant_id.unique'   => 'This variant is already in the order.',
            'quantity.required'   => 'Quantity is required.',
            'quantity.min'        => 'Quantity must be at least 1.',
            'price_type.in'       => 'Price type must be either sale or original.',
        ];
    }
}
