<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/patients', function () {
        return ['data' => 'List of patients'];
    });

    Route::middleware(['access.key'])->get('/secure-data', function () {
        return ['message' => 'You have access!'];
    });

});
