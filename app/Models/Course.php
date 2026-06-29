<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_code',
        'course_title',
        'unit',
        'level',
        'semester',
        'is_elective',
        'department_id',
        'status'
    ];

    protected function casts(): array
    {
        return ['is_elective' => 'boolean'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function courseRegistrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }
}
