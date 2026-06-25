<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    protected $fillable = [
        'name', 'is_active', 'current_semester',
        'registration_opens_at', 'registration_closes_at'
    ];

    protected function casts(): array
    {
        return [
            'is_active'              => 'boolean',
            'registration_opens_at'  => 'date',
            'registration_closes_at' => 'date',
        ];
    }

    public function courseRegistrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    public function isRegistrationOpen(): bool
    {
        if (!$this->registration_opens_at || !$this->registration_closes_at) {
            return true; // No window set — open by default
        }

        $today = now()->toDateString();
        return $today >= $this->registration_opens_at->toDateString()
            && $today <= $this->registration_closes_at->toDateString();
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
