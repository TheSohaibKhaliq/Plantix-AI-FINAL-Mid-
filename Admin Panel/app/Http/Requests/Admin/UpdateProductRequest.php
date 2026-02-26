<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'vendor_id'       => 'sometimes|required|exists:vendors,id',
            'category_id'     => 'nullable|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'name'            => 'sometimes|required|string|max:255',
            'description'     => 'nullable|string|max:5000',
            'price'           => 'sometimes|required|numeric|min:0',
            'discount_price'  => 'nullable|numeric|min:0',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
            'sort_order'      => 'integer|min:0',
            'stock_quantity'  => 'nullable|integer|min:0',
            'track_stock'     => 'boolean',
        ];
    }
}
