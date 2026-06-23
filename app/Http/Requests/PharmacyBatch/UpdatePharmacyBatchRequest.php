<?php

namespace App\Http\Requests\PharmacyBatch;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePharmacyBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pharmacy_item_id' => 'required|exists:pharmacy_items,id',
            'supplier_id'      => 'required|exists:suppliers,id',
            'batch_number'     => 'required|string|max:100|unique:pharmacy_batches,batch_number,' . $this->route('batchs'),
            'quantity'         => 'required|integer|min:1',
            'production_date'  => 'nullable|date',
            'expiry_date'      => 'required|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'pharmacy_item_id.required' => 'Please select a pharmacy item.',
            'supplier_id.required'      => 'Please select a supplier.',
            'batch_number.required'     => 'Batch number is required.',
            'batch_number.string'       => 'Batch number must be a string.',
            'batch_number.unique'       => 'This batch number already exists.',
            'batch_number.max'          => 'Batch number must not exceed 100 characters.',
            'quantity.required'         => 'Quantity is required.',
            'quantity.integer'          => 'Quantity must be an integer.',
            'quantity.min'              => 'Quantity must be at least 1.',
            'production_date.date'      => 'Production date must be a valid date.',
            'expiry_date.required'      => 'Expiry date is required.',
            'expiry_date.date'          => 'Expiry date must be a valid date.',
            'expiry_date.after'         => 'Expiry date must be in the future.',
        ];
    }
}
