<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DispatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'dispatch_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:dispatch_date',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string|max:50',
            'items.*.brand' => 'nullable|string|max:100',
            'items.*.model' => 'nullable|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate_per_day' => 'required|numeric|min:0',   // 👈 ADD THIS LINE

        ];
    }
}
