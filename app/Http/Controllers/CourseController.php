<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\{Course, Student, Academic_session, CourseRegistration};
use function Pest\Laravel\json;

use Illuminate\Validation\Rules\In;

use App\Models\AcademicSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;



class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('department')->get();
        return response()->json($courses);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());
        return response()->json($course, 201);
    }

    public function show(Course $course)
    {
        return response()->json($course);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());
        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->noContent();
    }

    public function deptLevelcourses(Request $request, $id, $semester, $level)
    {

        $dept_Id = $id;

        $courses = Course::with('department')->where('department_id', $dept_Id)->where("level", $level)->where("semester", $semester)->get();

        if(!$courses) {
            return response()->json(["message" => "No courses found for the specified department and level"], 404);
        }
        $data = $courses->map(function($course) {
            return [
                "id" => $course->id,
                "course_code" => $course->course_code,
                "course_title" => $course->course_title,
                "level" => $course->level,
                "semester" => $course->semester,
                "unit" => $course->unit,
                "department_id" => $course->department_id,
                "department" => $course->department->name ?? null,
            ];
        });

        return response()->json($data);
    }

    public function registerCourses(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id'               => ['sometimes', 'integer', Rule::exists('students', 'id')],
            'courses'                  => ['required', 'array', 'min:1'],
            'courses.*.course_id'      => ['required', 'integer', Rule::exists('courses', 'id')],
            'courses.*.semester'       => ['required', 'string', Rule::in(['1st', '2nd'])],
        ]);

        // Use authenticated user or fallback to payload student_id
        $studentId = Auth::id() ?? $validated['student_id'];

        $academicSession = Academic_session::where('is_active', 1)->first();
        if (!$academicSession) {
            return response()->json([
                'success' => false,
                'message' => 'There is no active academic session.',
            ], 422);
        }

        // Extract course IDs from the courses array
        $incomingCourseIds = array_column($validated['courses'], 'course_id');

        // Check for duplicate registrations per course AND semester combo
        $alreadyRegistered = CourseRegistration::where('student_id', $studentId)
            ->where('academic_session_id', $academicSession->id)
            ->whereIn('course_id', $incomingCourseIds)
            ->whereIn(
                // Match semester per course dynamically
                'course_id',
                collect($validated['courses'])
                    ->filter(fn($c) => CourseRegistration::where('student_id', $studentId)
                        ->where('academic_session_id', $academicSession->id)
                        ->where('course_id', $c['course_id'])
                        ->where('semester', $c['semester'])
                        ->exists()
                    )
                    ->pluck('course_id')
                    ->toArray()
            )
            ->pluck('course_id')
            ->toArray();

        if (!empty($alreadyRegistered)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already registered for one or more selected courses in this semester.',
                'data'    => ['duplicate_course_ids' => $alreadyRegistered],
            ], 409);
        }

        DB::beginTransaction();

        try {
            $registrations = [];

            foreach ($validated['courses'] as $course) {
                $registrations[] = CourseRegistration::create([
                    'student_id'          => $studentId,
                    'course_id'           => $course['course_id'],
                    'academic_session_id' => $academicSession->id,
                    'semester'            => $course['semester'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course registration successful.',
                'data'    => $registrations,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Course registration failed', [
                'student_id' => $studentId,
                'payload'    => $validated,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Course registration failed. Please try again.',
            ], 500);
        }
    }
}
