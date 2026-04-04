<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;

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
}
