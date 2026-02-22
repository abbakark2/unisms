<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $this->route('course')->id,
            'course_title' => 'required|string|max:255',
            'unit' => 'required|integer|min:1|max:6',
            'level' => 'required|integer|min:1|max:7',
            'semester' => 'required|in:1st,2nd',
            'department_id' => 'required|exists:departments,id',
        ];
    }
}
