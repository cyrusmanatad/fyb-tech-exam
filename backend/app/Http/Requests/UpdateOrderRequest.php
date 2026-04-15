<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {  
        return [
            'status' => [Rule::enum(OrderStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
        'status.enum' => 'The selected status is invalid. Please choose a valid order status.',
        ];
    }
}
