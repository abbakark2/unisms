<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseRegistrationService
{
    const MAX_UNITS = 24;

    // ----------------------------------------------------------------
    // PREVIEW — no DB writes, just intelligence
    // ----------------------------------------------------------------
    public function preview(Student $student): array
    {
        $session = AcademicSession::getActive();

        if (!$session) {
            throw new \RuntimeException('No active academic session found.');
        }

        if (!$session->current_semester) {
            throw new \RuntimeException('No active semester set for the current session.');
        }

        // What the student has already registered this session
        $alreadyRegistered = $this->getAlreadyRegistered($student, $session->id);

        $alreadyRegisteredCourseIds = $alreadyRegistered->pluck('course_id');

        // Current level courses for their dept and the active semester
        $currentCourses = Course::where('department_id', $student->department_id)
            ->where('level', $student->level)
            ->where('semester', $session->current_semester)
            ->whereNotIn('id', $alreadyRegisteredCourseIds)
            ->get();

        // Carryover candidates
        $carryoverCourses = $this->detectCarryovers($student, $alreadyRegisteredCourseIds);

        // Units already locked in from registered courses
        $registeredUnits = $alreadyRegistered->sum('course.unit');

        return [
            'session'             => $session,
            'already_registered'  => $alreadyRegistered,
            'suggested_current'   => $currentCourses,
            'suggested_carryover' => $carryoverCourses,
            'registered_units'    => $registeredUnits,
            'remaining_units'     => self::MAX_UNITS - $registeredUnits,
        ];
    }

    // ----------------------------------------------------------------
    // REGISTER — writes to DB
    // ----------------------------------------------------------------
    public function register(
        Student $student,
        array $courses, // [['course_id' => 1, 'is_carryover' => false], ...]
        int $registeredBy
    ): array {
        $session = AcademicSession::getActive();

        if (!$session) {
            throw new \RuntimeException('No active academic session found.');
        }

        if (!$session->isRegistrationOpen()) {
            throw new \RuntimeException('Course registration is currently closed.');
        }

        $courseIds = array_column($courses, 'course_id');

        // Validate all course IDs exist and belong to correct department or student's history
        $validCourses = Course::whereIn('id', $courseIds)->get()->keyBy('id');

        if ($validCourses->count() !== count($courseIds)) {
            throw new \InvalidArgumentException('One or more course IDs are invalid.');
        }

        // Check for already registered courses this session
        $alreadyRegistered = CourseRegistration::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->whereIn('course_id', $courseIds)
            ->pluck('course_id')
            ->toArray();

        if (!empty($alreadyRegistered)) {
            $codes = $validCourses->whereIn('id', $alreadyRegistered)->pluck('course_code');
            throw new \RuntimeException(
                'Already registered: ' . $codes->implode(', ')
            );
        }

        // Enforce credit unit cap
        $existingUnits = CourseRegistration::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->join('courses', 'courses.id', '=', 'course_registrations.course_id')
            ->sum('courses.unit');

        $incomingUnits = $validCourses->sum('unit');

        if (($existingUnits + $incomingUnits) > self::MAX_UNITS) {
            throw new \RuntimeException(
                "Registering these courses would exceed the {self::MAX_UNITS} unit limit. " .
                "You have {$existingUnits} units already registered."
            );
        }

        return DB::transaction(function () use (
            $student, $courses, $validCourses, $session, $registeredBy
        ) {
            $registered = [];

            foreach ($courses as $item) {
                $course = $validCourses[$item['course_id']];

                $registered[] = CourseRegistration::create([
                    'student_id'          => $student->id,
                    'course_id'           => $course->id,
                    'academic_session_id' => $session->id,
                    'semester'            => $course->semester,
                    'is_carryover'        => $item['is_carryover'] ?? false,
                    'status'              => 'approved',
                    'registered_by'       => $registeredBy,
                ]);
            }

            return $registered;
        });
    }

    // ----------------------------------------------------------------
    // DROP a single registered course
    // ----------------------------------------------------------------
    public function drop(CourseRegistration $registration): void
    {
        $session = AcademicSession::getActive();

        if (!$session || !$session->isRegistrationOpen()) {
            throw new \RuntimeException('Course registration is currently closed.');
        }

        // Cannot drop if result already exists
        if ($registration->result()->exists()) {
            throw new \RuntimeException('Cannot drop a course that already has a result.');
        }

        $registration->delete();
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPERS
    // ----------------------------------------------------------------
    private function getAlreadyRegistered(Student $student, int $sessionId): Collection
    {
        return CourseRegistration::with('course')
            ->where('student_id', $student->id)
            ->where('academic_session_id', $sessionId)
            ->get();
    }

    private function detectCarryovers(Student $student, Collection $excludeCourseIds): Collection
    {
        // Courses the student registered in past sessions
        $pastRegistrations = CourseRegistration::with(['course', 'result'])
            ->where('student_id', $student->id)
            ->whereHas('course', fn($q) => $q
                ->where('department_id', $student->department_id)
                ->where('level', '<', $student->level)
            )
            ->whereNotIn('course_id', $excludeCourseIds)
            ->get();

        return $pastRegistrations
            ->filter(function ($registration) {
                // No result at all — never graded, treat as carryover
                if (!$registration->result) return true;

                // Failed or explicitly marked carryover
                return in_array($registration->result->status, ['fail', 'carryover']);
            })
            // Deduplicate — student may have registered same course multiple sessions
            ->unique('course_id')
            ->map(fn($r) => $r->course)
            ->values();
    }
}
