<?php

namespace App\Http\Requests\Expert;

use Illuminate\Foundation\Http\FormRequest;

class RejectAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('expert')->check();
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'A rejection reason is required to inform the farmer.',
            'reason.min'      => 'Please provide at least 10 characters explaining the rejection.',
        ];
    }
}
