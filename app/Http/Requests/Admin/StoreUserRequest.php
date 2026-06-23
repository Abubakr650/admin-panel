<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name'       => 'required|string|max:255|unique:users,name',
            'full_name'  => 'nullable|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'role'       => 'required|string',
            'phone'      => 'nullable|string|max:30',
            'address'    => 'nullable|string|max:500',
            'gender'     => 'required|in:male,female',
            'birth_date' => 'nullable|date',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'The username field is required.',
            'name.unique'         => 'The username has already been taken.',
            'name.max'            => 'The username may not be greater than 255 characters.',
            'name.string'         => 'The username must be a string.',
            'full_name.string'    => 'The full name must be a string.',
            'full_name.max'       => 'The full name may not be greater than 255 characters.',
            'email.required'      => 'The email field is required.',
            'email.unique'        => 'The email has already been taken.',
            'email.email'         => 'The email must be a valid email address.',
            'password.required'   => 'The password field is required.',
            'password.confirmed'  => 'The password confirmation does not match.',
            'password.min'        => 'The password must be at least 8 characters long.',
            'password.string'     => 'The password must be a string.',
            'role.required'       => 'The role field is required.',
            'role.string'         => 'The role must be a string.',
            'phone.string'        => 'The phone must be a string.',
            'phone.max'           => 'The phone may not be greater than 30 characters.',
            'address.string'      => 'The address must be a string.',
            'address.max'         => 'The address may not be greater than 500 characters.',
            'gender.required'     => 'The gender field is required.',
            'gender.in'           => 'The gender must be either male or female.',
            'birth_date.date'     => 'The birth date must be a valid date.',
            'image.image'         => 'The image must be an image file.',
            'image.mimes'         => 'The image must be a file of type: jpeg, png, jpg, webp.',
            'image.max'           => 'The image may not be greater than 5MB.',
        ];
    }
}
