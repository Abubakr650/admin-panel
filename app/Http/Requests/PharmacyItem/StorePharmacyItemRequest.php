<?php

namespace App\Http\Requests\PharmacyItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePharmacyItemRequest extends FormRequest
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
            'commercial_name'     => [
                'required', 'string', 'max:255',
                Rule::unique('pharmacy_items', 'commercial_name')
                    ->where('company_name', $this->input('company_name')),
            ],
            'scientific_name'     => 'required|string|max:255',
            'company_name'        => 'required|string|max:255',
            'form'                => 'required|string|in:tablet,capsule,syrup,cream,ointment,injection,suspension,drops',
            'category'            => 'required|string|in:medicine,supplement,cosmetic,other',
            'qr_code'             => 'nullable|string|max:255|unique:pharmacy_items,qr_code',
            'location_in_pharmacy'=> 'nullable|string|max:255',
            'notes'               => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'commercial_name.required'    => 'The commercial name field is required.',
            'commercial_name.string'      => 'The commercial name field must be a string.',
            'commercial_name.unique'      => 'This drug already exists under the same company name.',
            'commercial_name.max'         => 'The commercial name must not exceed 255 characters.',
            'scientific_name.required'    => 'The scientific name field is required.',
            'scientific_name.string'      => 'The scientific name field must be a string.',
            'scientific_name.max'         => 'The scientific name must not exceed 255 characters.',
            'company_name.required'       => 'The company name field is required.',
            'company_name.string'         => 'The company name field must be a string.',
            'company_name.max'            => 'The company name must not exceed 255 characters.',
            'form.required'               => 'The form field is required.',
            'form.string'                 => 'The form field must be a string.',
            'form.max'                    => 'The form must not exceed 255 characters.',
            'category.required'           => 'The category field is required.',
            'category.string'             => 'The category field must be a string.',
            'category.max'                => 'The category must not exceed 255 characters.',
            'category.in'                 => 'The category field must be one of: medicine,supplement,cosmetic,other.',
            'qr_code.string'              => 'The QR code field must be a string.',
            'qr_code.unique'              => 'The QR code already exists.',
            'qr_code.max'                 => 'The QR code must not exceed 255 characters.',
            'location_in_pharmacy.string' => 'The location field must be a string.',
            'location_in_pharmacy.max'    => 'The location field must not exceed 255 characters.',
            'notes.string'                => 'The notes field must be a string.',
            'notes.max'                   => 'The notes field must not exceed 2000 characters.',
        ];
    }
}
