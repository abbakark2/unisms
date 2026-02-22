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
            // 'user_id' => 'required|exists:users,id',
            'matric_number' => 'required|string|unique:students,matric_number|min:5',
            'admission_year' => 'required|integer|min:1900|max:' . date('Y'),
            'graduation_year' => 'required|integer|gte:admission_year|max:' . (date('Y') + 10),
            'current_level' => 'required|integer',
            'status' => 'required|in:active,inactive,spillover,graduated,withdrawn',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'date',
            'gender' => 'required|in:male,female,other',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'phone'=>'unique:users,phone',
            'email' => 'email',
            'mode_entry' => 'required|in:UTME,DE,Other',
        ];
    }
}
