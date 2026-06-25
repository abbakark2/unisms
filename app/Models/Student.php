<?php

namespace App\Models;

use App\Models\{User, Result, Department, AcademicSession};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CourseRegistration;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'matric_number',
        'admission_year',
        'graduation_year',
        'level',
        'gender',
        'entry_session_id',
        'status',
        'mode_entry',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function entrySession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'entry_session_id');
    }

    public function courseRegistrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    // All courses registered in a specific session
    public function registrationsForSession(int $sessionId): HasMany
    {
        return $this->courseRegistrations()
            ->where('academic_session_id', $sessionId);
    }
}
