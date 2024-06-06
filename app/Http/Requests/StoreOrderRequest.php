<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'user_id' => 'required|exists:users,id',
            'device_id' => 'required|exists:devices,id',
            'payment_type' => 'required|integer',
            'payment_day' => 'required|integer|min:1|max:31',
            'body_price' => 'required|integer',
            'summa' => 'required|integer',
            'initial_payment' => 'required|integer',
            'box' => 'required|integer',
            'order_date' => 'required|date',
        ];
    }
}
