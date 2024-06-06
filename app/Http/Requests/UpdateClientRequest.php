<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'surname' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'passport' => 'sometimes|required|string|max:255',
            'file_passport' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Assuming it's an image
            'gender' => 'sometimes|required|integer|in:0,1', // Example: 0 for male, 1 for female
            'workplace' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|required|string|max:255',
        ];
    }
}
