<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        // Normalize email before validation to prevent duplicate accounts
        // that differ only in casing.
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->input('email'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'email:rfc,dns', 'unique:users,email', 'max:255'],
            'phone'    => ['nullable', 'string', 'regex:/^[\+\d\s\-\(\)]{7,30}$/', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->uncompromised()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'     => 'An account with this email already exists.',
            'email.email'      => 'Please enter a valid email address.',
            'phone.regex'      => 'Phone number format is invalid. Use digits, spaces, +, -, or parentheses.',
            'password.min'     => 'Password must be at least 8 characters.',
            'password.letters' => 'Password must contain at least one letter.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.uncompromised' => 'This password has appeared in a data breach. Please choose a safer password.',
            'name.min'         => 'Name must be at least 2 characters.',
        ];
    }
}
