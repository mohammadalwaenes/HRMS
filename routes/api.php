<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\employee\EmployeeController;
use App\Http\Controllers\trainee\TraineeController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\position\PositionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UuidController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make sure sanctum is used.
|
*/

// ----------------- AUTH -----------------
Route::prefix('auth')->group(function () {
    Route::post('login', [LoginController::class, 'auth']); // login

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']); // logout
        Route::get('me', function(Request $request) {
            return response()->json($request->user());
        });
    });
});

// ----------------- MODERATOR ROUTES -----------------
Route::middleware(['auth:sanctum', 'can:moderator'])->prefix('moderator')->group(function () {
    Route::get('/', [ModeratorController::class, 'index']);
    Route::post('/admin/toggle-active/{id}', [ModeratorController::class, 'toggleActive']);
    Route::post('/admin/add', [ModeratorController::class, 'addAdmin']);
    Route::delete('/admin/{id}', [ModeratorController::class, 'deleteAdmin']);
    Route::get('/admin/{id}', [ModeratorController::class, 'editAdmin']);
    Route::put('/admin/{id}', [ModeratorController::class, 'update']);
});

// ----------------- ADMIN ROUTES -----------------
Route::middleware(['auth:sanctum', 'can:admin', 'active.admin'])->group(function () {

    // Employee routes
    Route::apiResource('employees', EmployeeController::class);
    Route::get('employees/statistics', [EmployeeController::class, 'showEmployeeStats']);
    Route::post('employees/{id}/assign-schedule', [EmployeeController::class, 'AssignSchedule']);
    Route::put('employees/{id}/unassign-schedule', [EmployeeController::class, 'unAssignSchedule']);
    Route::get('employees/terminated', [EmployeeController::class, 'showTerminated']);
    Route::get('employees/terminated/{id}', [EmployeeController::class, 'showTerminatedEmployee']);
    Route::post('employees/{id}/cv/upload', [EmployeeController::class, 'uploadCv']);
    Route::delete('employees/{id}/cv', [EmployeeController::class, 'deleteCv']);
    Route::post('employees/{id}/image/upload', [EmployeeController::class, 'uploadImage']);
    Route::delete('employees/{id}/image', [EmployeeController::class, 'deleteImage']);
    Route::post('employees/{id}/restore', [EmployeeController::class, 'restore']);

    // Trainee routes
    Route::apiResource('trainees', TraineeController::class);
    Route::get('trainees/{id}/confirm-hire', [TraineeController::class, 'confirm']);
    Route::post('trainees/{id}/hire', [TraineeController::class, 'hire']);
    Route::get('trainees/{id}/end-training-pdf', [TraineeController::class, 'downloadEndTrainingPdf']);
    Route::delete('trainees/{id}/end-training', [TraineeController::class, 'endTraining']);
    Route::get('trained', [TraineeController::class, 'trainedIndex']);
    Route::get('trained/{id}', [TraineeController::class, 'showTrained']);

    // Vacation routes
    Route::apiResource('vacations', VacationController::class);
    Route::get('vacations/statistics', [VacationController::class, 'statistics']);

    // Position routes
    Route::apiResource('positions', PositionController::class);
    Route::get('positions/{position}/employees', [PositionController::class, 'showEmployeeByPosition']);

    // Schedule routes
    Route::apiResource('schedules', ScheduleController::class);
    Route::get('schedules/{id}/employees', [ScheduleController::class, 'assignedEmployees']);
    Route::get('schedules/statistics', [ScheduleController::class, 'showStatistics']);

    // UUID check
    Route::post('uuid/check', [UuidController::class, 'checkUuid']);

});