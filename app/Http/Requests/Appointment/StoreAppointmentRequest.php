<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'patient_id'            => 'required|exists:patients,id',
            'doctor_id'             => 'required|exists:doctors,id',
            'parent_appointment_id' => 'nullable|exists:appointments,id',
            'appointment_date'      => 'required|date',
            'appointment_time'      => 'required',
            'appointment_status'    => 'required|in:scheduled,completed,cancelled,no_show',
            'appointment_notes'     => 'nullable|string',
        ];
    }

     public function messages(): array
    {
        return [
            'patient_id.required'       => 'The patient field is required.',
            'patient_id.exists'         => 'The patient does not exist.',
            'doctor_id.required'        => 'The doctor field is required.',
            'doctor_id.exists'          => 'The doctor does not exist.',
            'parent_appointment_id.exists' => 'The parent appointment does not exist.',
            'appointment_date.required' => 'The appointment date is required.',
            'appointment_date.date'     => 'The appointment date must be a valid date.',
            'appointment_time.required' => 'The appointment time is required.',
            'appointment_status.required' => 'The appointment status is required.',
            'appointment_status.in'     => 'The appointment status must be either scheduled, completed, cancelled, or no_show.',
            'appointment_notes.string'  => 'The appointment notes must be a string.',
        ];
    }
}
