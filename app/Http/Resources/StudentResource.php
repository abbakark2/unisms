<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'matric_number' => $this->matric_number,
            'admission_year' => $this->admission_year,
            'graduation_year' => $this->graduation_year,
            'current_level' => $this->current_level,
            'mode_entry' => $this->mode_entry,
            'status' => $this->status,
            'middle_name' => $this->middle_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
        ];
    }
}
