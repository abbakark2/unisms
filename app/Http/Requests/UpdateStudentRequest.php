<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $studentId = $this->route('student')->id;
        return [
        // Name and Email are needed here because they come from the UI
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            // Ignore the user associated with this student
            Rule::unique('users')->ignore($this->route('student')->user_id)
        ],
        'matric_number' => [
            'required',
            'string',
            Rule::unique('students')->ignore($studentId),
        ],
        'admission_year' => 'required|integer|min:1900',
        'current_level'  => 'required|integer',
                        Rule::in([100, 200, 300, 400, 500, 600]),
        'graduation_year' => 'nullable|integer|gte:admission_year|max:' . (date('Y') + 10),
        'status' => 'required|in:active,inactive,spillover,graduated,withdrawn',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'entry_session_id' => 'nullable|exists:sessions,id',
        'faculty_id' => 'nullable|exists:faculties,id',
        'department_id' => 'nullable|exists:departments,id',
        ];
    }

    public function studentData(): array
    {
        return $this->safe()->only([
            'matric_number',
            'admission_year',
            'graduation_year',
            'status',
            'mode_entry',
            'level',
            'gender',
        ]);
    }

    public function userData(): array
    {
        return $this->safe()->only([
            'name',
            'email',
            'phone',
            'dob',
            'faculty_id',
            'department_id',
        ]);
    }
}
