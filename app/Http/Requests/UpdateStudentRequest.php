<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'matric_number' => 'required|string|unique:students,matric_number,' . $this->route('student'),
            'admission_year' => 'required|integer|min:1900|max:' . date('Y'),
            'graduation_year' => 'nullable|integer|gte:admission_year|max:' . (date('Y') + 10),
            'current_level' => 'required|integer|min:1|max:10',
            'status' => 'required|in:active,spillover,graduated,withdrawn',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'entry_session_id' => 'nullable|exists:sessions,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }
}
