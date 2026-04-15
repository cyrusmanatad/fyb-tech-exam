<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            // Order Info
            'notes'          => ['nullable', 'string', 'max:500'],
            'currency'       => ['nullable', 'string', 'size:3'],

            // Payment
            'payment_method' => ['nullable', 'string', Rule::in([
                'cash', 'credit_card', 'debit_card', 'gcash', 'paymaya', 'bank_transfer'
            ])],

            // Shipping
            'shipping_method' => ['nullable', 'string'],
            'shipping_fee'    => ['nullable', 'numeric', 'min:0'],

            // Discount
            'discount' => ['nullable', 'numeric', 'min:0'],

            // Tax
            'tax' => ['nullable', 'numeric', 'min:0'],

            // Order Items
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.variant_id'        => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.quantity'          => ['required', 'integer', 'min:1'],
            'items.*.price_type'        => ['required', 'string', Rule::in(['sale', 'original'])],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'               => 'Order must have at least one item.',
            'items.min'                    => 'Order must have at least one item.',
            'items.*.variant_id.required'  => 'Each item must have a valid variant.',
            'items.*.variant_id.exists'    => 'One or more selected variants do not exist.',
            'items.*.quantity.required'    => 'Each item must have a quantity.',
            'items.*.quantity.min'         => 'Quantity must be at least 1.',
            'items.*.price_type.required'  => 'Each item must have a price type.',
            'items.*.price_type.in'        => 'Price type must be either sale or original.',
        ];
    }
}
