<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    /**
     * Customer checkout. Money fields are not accepted here.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:500'],
            'currency' => ['nullable', 'string', 'size:3'],
            'payment_method' => ['required', 'string', Rule::in([
                'cash', 'credit_card', 'debit_card', 'gcash', 'paymaya', 'bank_transfer',
            ])],
            'shipping_method' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price_type' => ['required', 'string', Rule::in(['sale', 'original'])],
        ];
    }
}
