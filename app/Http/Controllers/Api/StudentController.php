<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\{Student, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('user', 'user.department')->paginate(10);
        return response()->json($students);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        try {
            $student = DB::transaction(function () use ($request) {

                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => bcrypt($request->password),
                    'role_id' => 4,
                    'department_id' => $request->department_id,
                    'phone' => $request->phone,
                    'is_active' => 1,
                    'dob' => $request->date_of_birth,
                    'faculty_id' => $request->faculty_id,

                ]);

                $student = Student::create([
                    'user_id'           => $user->id,
                    'matric_number'       => $request->matric_number,
                    'admission_year'      => $request->admission_year,
                    'graduation_year'     => $request->graduation_year,
                    'status'              => $request->status,
                    'mode_entry'          => $request->mode_entry,
                    'level' => $request->current_level,

                    // 'course'            => $request->course,
                    // 'year_level'        => $request->year_level,
                    // 'section'           => $request->section,
                    // 'phone'             => $request->phone,
                    // 'address'           => $request->address,
                    // 'date_of_birth'     => $request->date_of_birth,
                    'gender'            => $request->gender,
                    // 'enrollment_status' => $request->enrollment_status ?? 'pending',
                ]);

                return $student->load('user');
            });

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully.',
                'data'    => $student,
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            // Database-level errors: constraint violations, duplicate entries, connection issues
            Log::error('Database error while creating student', [
                'error'      => $e->getMessage(),
                'error_code' => $e->getCode(),
                'sql'        => $e->getSql(),
                'bindings'   => $e->getBindings(),
                'request'    => $request->except(['password', 'password_confirmation']),
                'user_id'    => auth()->id() ?? null,
                'ip'         => $request->ip(),
                'trace'      => $e->getTraceAsString(),
            ]);

            // Handle unique constraint violation specifically (e.g. duplicate email or student_no)
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'A student with this email or student number already exists.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'A database error occurred. Please try again later.',
            ], 500);

        } catch (\Exception $e) {
            // Catch-all for unexpected errors (logic errors, third-party failures, etc.)
            Log::critical('Unexpected error while creating student', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'request' => $request->except(['password', 'password_confirmation']),
                'user_id' => auth()->id() ?? null,
                'ip'      => $request->ip(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please contact support if this persists.',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with('user', 'department')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        try {

            DB::transaction(function () use ($request, $student) {

                $student->update($request->studentData());

                $student->user->update($request->userData());

            });

            return response()->json(
                $student->load('user')
            );

        } catch (\Throwable $e) {

            Log::error('Student update failed', [
                'student_id' => $student->id,
                'request'    => $request->safe()->toArray(),
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'user_id'    => auth()->id(),
                'ip'         => $request->ip(),
            ]);

            throw $e; // IMPORTANT — rethrow it
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }

    /**
     * Get statistics for students.
     */
    public function stats()
    {
        $total = Student::count();
        $graduated = Student::where('status', 'graduated')->count();
        $spillover = Student::where('status', 'spillover')->count();
        $inactive = Student::where('status', 'inactive')->count();

        return response()->json([
            'total' => $total,
            'graduated' => $graduated,
            'spillover' => $spillover,
            'inactive' => $inactive,
        ]);
    }
}
