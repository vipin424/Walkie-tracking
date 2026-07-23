<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->filled('event_time') && strlen($this->event_time) === 8) {
            $this->merge(['event_time' => substr($this->event_time, 0, 5)]);
        }
    }

    public function rules(): array
    {
        return [
            'client_name'              => 'required|string',
            'client_email'             => 'nullable|email',
            'client_phone'             => 'required|digits:10',
            
            'event_from'               => 'required|date',
            'event_to'                 => 'required|date|after_or_equal:event_from',
            'event_time'               => 'nullable|date_format:H:i',
            'event_location'           => 'nullable|string|max:255',
            'handle_type'              => 'required|string',
            
            'items'                    => 'required|array|min:1',
            'items.*.item_name'        => 'required|string',
            'items.*.quantity'         => 'required|numeric|min:1',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.tax_percent'      => 'nullable|numeric|min:0',

            'extra_charge_type'        => 'nullable|string',
            'extra_charge_rate'        => 'nullable|numeric|min:0',
            'staff_count'              => 'nullable|integer|min:1',
            'delivery_charge_amount'   => 'nullable|numeric|min:0',
            'travelling_charge_amount' => 'nullable|numeric|min:0',
            
            'discount_amount'          => 'nullable|numeric|min:0',
            'advance_paid'             => 'required|numeric|min:0',
            'security_deposit'         => 'nullable|numeric|min:0',
            'notes'                    => 'nullable|string',
            'bill_to'                  => 'nullable|string',
        ];
    }
}
