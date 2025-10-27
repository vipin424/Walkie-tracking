<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequest extends FormRequest
{
    public function authorize(): bool { return true; }

public function rules(): array
    {
        return [
            'dispatch_id' => 'required|exists:dispatches,id',
            'return_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
            // items array required but we’ll validate each conditionally
            'items' => 'required|array|min:1',
            'items.*.dispatch_item_id' => 'required|exists:dispatch_items,id',
            'items.*.returned_qty' => [
                'nullable',   // allow blank
                'integer',
                'min:1'
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Ensure at least one item has a returned_qty
            $items = $this->input('items', []);
            $hasReturned = collect($items)->contains(function ($item) {
                return !empty($item['returned_qty']) && $item['returned_qty'] > 0;
            });

            if (!$hasReturned) {
                $validator->errors()->add('items', 'Please enter return quantity for at least one item.');
            }
        });
    }
}
