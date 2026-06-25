<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    //Get all Department
    public function show(Request $request)
    {
        $dept = Department::with('faculty')->get();

        if ($dept->isEmpty()) {
            return response()->json([
                "status" => false,
                "message" => "No departments found",
                "data" => []
            ], 404);
        }

        $departments = $dept->map(function ($department) {
            return [
                "id" => $department->id,
                "name" => $department->name,
                "faculty" => [
                    "id" => $department->faculty->id,
                    "name" => $department->faculty->name,
                    "abbreviation" => $department->faculty->abbreviation,
                ],
            ];
        });

        return response()->json([
            "status" => true,
            "message" => "Departments fetched successfully",
            "data" => $departments
        ], 200);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        $code = strtoupper(substr($request->name, 0, 3)) . rand(100, 999);

        $dept = Department::create([
            'name' => $request->name,
            'faculty_id' => $request->faculty_id,
            'code' => $code,
        ]);

        return response()->json(["message" => "Department added successfully", "Department" => $dept], 201);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        $department->name = $request->name;
        $department->faculty_id = $request->faculty_id;
        $department->save();

        return response()->json(["message" => "Department updated successfully", "Department" => $department], 200);
    }

    public function delete(Request $request, Department $department)
    {
        $department->delete();
        return response()->json(["message" => "Department deleted successfully"], 200);
    }
}
