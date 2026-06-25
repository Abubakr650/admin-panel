<?php

namespace App\Http\Requests\Patient;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
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
    protected function prepareForValidation(): void
    {
        if ($this->filled('age')) {
            $this->merge([
                'birth_date' => Carbon::now()->subYears((int) $this->input('age'))->format('Y-m-d'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'full_name'  => 'required|string|max:255',
            'gender'     => 'required|string|max:255',
            'phone'      => 'nullable|string|max:255',
            'address'    => 'nullable|string|max:255',
            'age'        => 'required|integer|min:1|max:120',
            'birth_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'full_name.required'  => 'The full name field is required.',
            'full_name.max'       => 'The full name field must not exceed 255 characters.',
            'full_name.string'    => 'The full name field must be a string.',
            'gender.required'     => 'The gender field is required.',
            'gender.string'       => 'The gender field must be a string.',
            'gender.max'          => 'The gender field must not exceed 255 characters.',
            'phone.string'        => 'The phone field must be a string.',
            'phone.max'           => 'The phone field must not exceed 255 characters.',
            'address.string'      => 'The address field must be a string.',
            'address.max'         => 'The address field must not exceed 255 characters.',
            'age.required'        => 'Age is required.',
            'age.integer'         => 'Age must be a whole number.',
            'age.min'             => 'Age must be at least 1.',
            'age.max'             => 'Age must not exceed 120.',
        ];
    }
}
