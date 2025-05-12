<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->group(function () {

    // Endpoint lain yang membutuhkan access key
    Route::middleware('access.key')->get('/secure-data', function () {
        return ['message' => 'You have access!'];
    });

    // Rute untuk manajemen pasien
    Route::prefix('patients')->middleware('access.key')->group(function () {
        // Get list of all patients
        Route::get('/', [PatientController::class, 'index']);

        // Create a new patient
        Route::post('/', [PatientController::class, 'store']);

        // Show a specific patient by ID
        Route::get('{id}', [PatientController::class, 'show']);

        // Update a specific patient by ID
        Route::put('{id}', [PatientController::class, 'update']);

        // Delete a specific patient by ID
        Route::delete('{id}', [PatientController::class, 'destroy']);
    });

});
