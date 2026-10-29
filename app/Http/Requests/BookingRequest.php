<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id'   => ['required', 'integer', 'exists:services,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'string', 'max:50'],
            'address'      => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ];
    }
}
