<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /** Access is already restricted by the 'admin' middleware on the routes. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // On create every main field is required; on update they are optional.
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:255'],
            'category' => [$required, 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => [$required, 'numeric', 'min:0', 'max:99999999'],
            // The rating column cannot be NULL (default 0): leave the field out
            // instead of sending null, otherwise the database would reject it.
            'rating' => ['sometimes', 'numeric', 'between:0,5'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
