<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255|unique:suppliers,name,' . $this->route('supplier'),
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'country' => 'nullable|string|max:100',
            'notes'   => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'The supplier name is required.',
            'name.unique'    => 'This supplier name already exists.',
            'name.string'    => 'The supplier name field must be a string.',
            'name.max'       => 'The supplier name field must not exceed 255 characters.',
            'phone.string'   => 'The phone field must be a string.',
            'phone.max'      => 'The phone field must not exceed 50 characters.',
            'email.string'   => 'The email field must be a string.',
            'email.max'      => 'The email field must not exceed 255 characters.',
            'address.string' => 'The address field must be a string.',
            'address.max'    => 'The address field must not exceed 1000 characters.',
            'country.string' => 'The country field must be a string.',
            'country.max'    => 'The country field must not exceed 100 characters.',
            'notes.string'   => 'The notes field must be a string.',
            'notes.max'      => 'The notes field must not exceed 5000 characters.',
        ];
    }
}
