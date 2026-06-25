<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Services\CourseRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRegistrationController extends Controller
{
    public function __construct(
        private readonly CourseRegistrationService $service
    ) {}

    // GET /api/registration/preview
    public function preview(): JsonResponse
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found.'], 404);
        }

        try {
            $data = $this->service->preview($student);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // POST /api/registration
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'courses'               => ['required', 'array', 'min:1'],
            'courses.*.course_id'   => ['required', 'integer', 'exists:courses,id'],
            'courses.*.is_carryover'=> ['sometimes', 'boolean'],
        ]);

        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found.'], 404);
        }

        try {
            $registrations = $this->service->register(
                $student,
                $validated['courses'],
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Courses registered successfully.',
                'data'    => $registrations,
            ], 201);

        } catch (\RuntimeException | \InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // DELETE /api/registration/{registration}
    public function drop(CourseRegistration $registration): JsonResponse
    {
        // Ensure student owns this registration
        $student = Auth::user()->student;

        if ($registration->student_id !== $student?->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $this->service->drop($registration);
            return response()->json(['success' => true, 'message' => 'Course dropped successfully.']);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // POST /api/admin/students/{student}/registration
    public function adminRegister(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'courses'                => ['required', 'array', 'min:1'],
            'courses.*.course_id'    => ['required', 'integer', 'exists:courses,id'],
            'courses.*.is_carryover' => ['sometimes', 'boolean'],
        ]);

        try {
            $registrations = $this->service->register(
                $student,
                $validated['courses'],
                Auth::id() // admin's user ID
            );

            return response()->json([
                'success' => true,
                'message' => 'Courses registered successfully by admin.',
                'data'    => $registrations,
            ], 201);

        } catch (\RuntimeException | \InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
