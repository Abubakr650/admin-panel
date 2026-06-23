<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
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
            'user_id'   => 'required|exists:users,id',
            'specialty' => 'required|string|max:255',
            'degree'    => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'   => 'The user field is required.',
            'user_id.exists'     => 'The selected user does not exist.',
            'specialty.required' => 'The specialty field is required.',
            'specialty.string'   => 'The specialty field must be a string.',
            'specialty.max'      => 'The specialty field must not exceed 255 characters.',
            'degree.required'    => 'The degree field is required.',
            'degree.string'      => 'The degree field must be a string.',
            'degree.max'         => 'The degree field must not exceed 255 characters.',
            'is_active.boolean'  => 'The is active field must be a boolean.',
        ];
    }
}
