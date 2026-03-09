<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'name' => 'required|string|min:3',
            'email' => 'email|unique:users,email',
            'phone' => 'nullable|string',
            'matric_number' => 'required|string|unique:students,matric_number',
            'admission_year' => 'required|integer|min:1900|max:' . date('Y'),
            'graduation_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'level' => 'required|integer',
            'dob' => 'date',
            'gender' => 'required|string|in:male,female,other',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
        ];
    }
}
