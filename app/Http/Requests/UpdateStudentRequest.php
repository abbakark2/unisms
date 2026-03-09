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
        'name' => 'required|string|min:3',
        'email' => [
            'required',
            'email',
            // Ignore the user associated with this student
            Rule::unique('users')->ignore($this->route('student')->user_id)
        ],
        'phone' => 'nullable|string',
        'matric_number' => [
            'required',
            'string',
            Rule::unique('students')->ignore($studentId),
        ],
        'admission_year' => 'required|integer|min:1900|max:' . date('Y'),
        'level'  => 'required|integer',
                        Rule::in([100, 200, 300, 400, 500, 600]),
        'graduation_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
        'status' => 'required|in:active,inactive,spillover,graduated,withdrawn',
        'dob' => 'required|date',
        'gender' => 'required|string|in:male,female,other',
        'faculty_id' => 'required|exists:faculties,id',
        'department_id' => 'required|exists:departments,id',
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
