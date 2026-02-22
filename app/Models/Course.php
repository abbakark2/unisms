<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_title',
        'unit',
        'level',
        'semester',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
