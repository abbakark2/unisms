<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matric_number',
        'admission_year',
        'graduation_year',
        'current_level',
        'status',
        'middle_name',
        'date_of_birth',
        'gender',
        'entry_session_id',
        'faculty_id',
        'department_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function results()
    {
        // return $this->hasMany(Result::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->user->name . ' ' . $this->middle_name);
    }
}
