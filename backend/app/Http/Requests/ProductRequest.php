<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'sku_code' => 'required|string|unique:products,sku_code',
            'sku_desc' => 'required|string',
            'sku_desc_long' => 'string',
            'sku_uom' => 'required|string',
            'sku_price' => 'required|numeric',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['sku_code'] = [
                'required',
                'string',
                'unique:products,sku_code,' . $this->route('product')->id,
            ];
        }

        return $rules;
    }
}
