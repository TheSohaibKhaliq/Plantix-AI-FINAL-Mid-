<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('vendor')->check();
    }

    public function rules(): array
    {
        return [
            'category_id'    => 'nullable|exists:categories,id',
            'name'           => 'sometimes|required|string|max:255',
            'description'    => 'nullable|string|max:5000',
            'price'          => 'sometimes|required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'      => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'track_stock'    => 'boolean',
        ];
    }
}
