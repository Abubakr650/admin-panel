<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWarehouseItemRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name'                  => [
                'required', 'string', 'max:255',
                Rule::unique('warehouse_items', 'name')
                    ->where('company_name', $this->input('company_name')),
            ],
            'company_name'          => 'nullable|string|max:255',
            'type'                  => 'nullable|string|max:100',
            'quantity'              => 'required|integer|min:0',
            'supplier_id'           => 'required|exists:suppliers,id',
            'production_date'       => 'nullable|date',
            'expiry_date'           => 'required|date|after:today',
            'category'              => 'required|string|in:chemical,equipment,packaging,other',
            'qr_code'               => 'nullable|string|max:255|unique:warehouse_items,qr_code',
            'location_in_warehouse' => 'nullable|string|max:255',
            'notes'                 => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Item name is required.',
            'name.string'        => 'Item name must be a string.',
            'name.unique'          => 'This item already exists under the same company name.',
            'name.max'             => 'Item name must not exceed 255 characters.',
            'company_name.string'  => 'Company name must be a string.',
            'company_name.max'     => 'Company name must not exceed 255 characters.',
            'type.string'          => 'Type must be a string.',
            'type.max'             => 'Type must not exceed 100 characters.',
            'quantity.required'    => 'Quantity is required.',
            'quantity.integer'     => 'Quantity must be an integer.',
            'quantity.min'         => 'Quantity must be at least 0.',
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists'   => 'Please select a valid supplier.',
            'category.required'    => 'Please select a category.',
            'category.in'          => 'Invalid category selected.',
            'qr_code.unique'       => 'This QR code is already assigned to another item.',
            'qr_code.string'       => 'QR code must be a string.',
            'qr_code.max'          => 'QR code must not exceed 255 characters.',
            'location_in_warehouse.string' => 'Location must be a string.',
            'location_in_warehouse.max'    => 'Location must not exceed 255 characters.',
            'notes.string'         => 'Notes must be a string.',
            'expiry_date.required' => 'Expiry date is required.',
            'expiry_date.after'    => 'Expiry date must be in the future.',
            'production_date.date' => 'Production date must be a valid date.',
            'expiry_date.date'     => 'Expiry date must be a valid date.',
            'qr_code.unique'       => 'This QR code is already assigned to another item.',
        ];
    }
}
