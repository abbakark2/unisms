<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function getFaculties()
    {
        $faculties = Faculty::get();
        return response()->json(["Faculties" => $faculties], 200);
    }
    public function getFacultiesData()
    {
        $faculties = Faculty::with('departments')->get();
        return response()->json(["Faculties" => $faculties], 200);
    }

    public function addFaculty(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10|unique:faculties,abbreviation',
        ]);

        $faculty = new Faculty();
        $faculty->name = $request->name;
        $faculty->abbreviation = strtoupper($request->abbreviation);
        $faculty->save();

        return response()->json(["message" => "Faculty added successfully", "Faculty" => $faculty], 201);
    }

    /**
     * Update the specified faculty in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Unique validation but ignores the current faculty ID
            'abbreviation' => 'required|string|max:10|unique:faculties,abbreviation,' . $faculty->id,
        ]);

        $faculty->name = $request->name;
        $faculty->abbreviation = strtoupper($request->abbreviation);
        $faculty->save();

        return response()->json([
            "message" => "Faculty updated successfully",
            "Faculty" => $faculty
        ], 200);
    }

    public function delete(Request $request, Faculty $faculty){
        $faculty->delete();
        return response()->json(["message" => "Faculty deleted successfully"]);

    }

}
