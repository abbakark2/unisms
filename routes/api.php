<?php

use App\Http\Controllers\Api\Academic_sessionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', [AuthController::class, "getUser"] )->middleware('auth:sanctum');

//public route
Route::post('/login', [AuthController::class, "Login"]);

//protected route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, "Dashboard"]);
    Route::post('/logout', [AuthController::class, "Logout"]);

    // Faculty Routes
    Route::get('/admin/faculties', [FacultyController::class, "getFaculties"]);
    Route::get('/admin/faculties/data', [FacultyController::class, "getFacultiesData"]);
    Route::post('/admin/faculty', [FacultyController::class, "addFaculty"]);
    Route::put('/admin/faculty/{faculty}', [FacultyController::class, "update"]);
    Route::delete('/admin/faculty/{faculty}', [FacultyController::class, "delete"]);
    Route::get('/admin/faculties/{faculty}/departments', [FacultyController::class, "getDepartmentsByFacultyId"]);
});

// Department
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/admin/dept', [DepartmentController::class, "show"]);
    Route::post('/admin/departments', [DepartmentController::class, "add"]);
    Route::put('/admin/departments/{department}', [DepartmentController::class, "update"]);
    Route::delete('/admin/departments/{department}', [DepartmentController::class, "delete"]);
});

// Student
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/students', [StudentController::class, 'index']);
    Route::post('/admin/students', [StudentController::class, 'store']);
    // Route::get('/admin/students/{student}', [StudentController::class, 'show']);
    Route::get('/student', [StudentController::class, 'show']);
    Route::put('/admin/students/{student}', [StudentController::class, 'update']);
    Route::delete('/admin/students/{student}', [StudentController::class, 'destroy']);
    Route::get('/admin/students/stats', [StudentController::class, 'stats']);
});

// Course
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/courses', CourseController::class);
    Route::get('/{department}/courses/{semester}/{level}', [CourseController::class, "deptLevelCourses"]);
});

// Academic Sessions Routes
Route::middleware('auth:sanctum')->group(function (){
    Route::get('/academic/session', [Academic_sessionController::class, 'Index']);
});
