<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'delivery_address' => 'required|string|max:1000',
            'payment_method'   => 'required|in:cod,stripe,wallet',
            'coupon_code'      => 'nullable|string|max:50',
            'notes'            => 'nullable|string|max:500',
            'delivery_fee'     => 'nullable|numeric|min:0',
        ];
    }
}
