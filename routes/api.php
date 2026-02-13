<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//public route
Route::post('/login', [AuthController::class, "Login"]);

//protected route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, "Dashboard"]);
    Route::post('/logout', [AuthController::class, "Logout"]);
    Route::get('/admin/faculties', [FacultyController::class, "getFaculties"]);
    Route::get('/admin/faculties/data', [FacultyController::class, "getFacultiesData"]);
    Route::post('/admin/faculties', [FacultyController::class, "addFaculty"]);
    Route::put('/admin/faculty/{faculty}', [FacultyController::class, "update"]);
    Route::delete('/admin/faculty/{faculty}', [FacultyController::class, "delete"]);
});

// Department Protected Route
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/admin/dept', [DepartmentController::class, "show"]);
    Route::post('/admin/dept', [DepartmentController::class, "add"]);
    Route::put('/admin/dept/{department}', [DepartmentController::class, "update"]);
    Route::delete('/admin/dept/{department}', [DepartmentController::class, "delete"]);
});
