<?php

namespace App\Http\Requests\Expert;

use Illuminate\Foundation\Http\FormRequest;

class AcceptAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('expert')->check();
    }

    public function rules(): array
    {
        return [
            'meeting_link' => ['nullable', 'url', 'max:500'],
        ];
    }
}
