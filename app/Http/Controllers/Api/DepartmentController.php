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
        // 1. Get the sort option or fall back to 'name_asc' (Alphabetical A-Z) as a sensible default
        $sort = $request->input('sort', 'name_asc');

        $query = Department::with('faculty');

        // 2. Apply sorting logic based on the parameter value
        switch ($sort) {
            case 'newest':
                $query->latest(); // tracks created_at DESC
                break;
            case 'oldest':
                $query->oldest(); // tracks created_at ASC
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $dept = $query->get();

        if ($dept->isEmpty()) {
            return response()->json([
                "status" => false,
                "message" => "No departments found",
                "data" => []
            ], 404);
        }

        // 3. Map out data using PHP's null-safe operator (?->) to prevent 500 errors if a faculty is missing
        $departments = $dept->map(function ($department) {
            return [
                "id" => $department->id,
                "name" => $department->name,
                "faculty" => [
                    "id" => $department->faculty?->id ?? null,
                    "name" => $department->faculty?->name ?? 'Unassigned',
                    "abbreviation" => $department->faculty?->abbreviation ?? '—',
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
