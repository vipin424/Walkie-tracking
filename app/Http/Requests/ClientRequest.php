<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
        ];
    }
}
