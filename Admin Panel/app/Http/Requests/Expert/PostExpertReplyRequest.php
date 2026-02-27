<?php

namespace App\Http\Requests\Expert;

use Illuminate\Foundation\Http\FormRequest;

class PostExpertReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('expert')->check();
    }

    public function rules(): array
    {
        return [
            'body'           => ['required', 'string', 'min:20', 'max:5000'],
            'recommendation' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Reply body is required.',
            'body.min'      => 'Expert replies should be at least 20 characters.',
        ];
    }
}
