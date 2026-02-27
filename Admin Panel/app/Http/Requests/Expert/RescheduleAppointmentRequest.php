<?php

namespace App\Http\Requests\Expert;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('expert')->check();
    }

    public function rules(): array
    {
        return [
            'proposed_datetime' => ['required', 'date', 'after:now'],
            'reason'            => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'proposed_datetime.required' => 'Please provide a new date and time.',
            'proposed_datetime.after'    => 'The proposed date/time must be in the future.',
        ];
    }
}
