<?php

use App\Http\Controllers\ScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'schedule'], function () {
    Route::post('/show/{schedule}', [ScheduleController::class, 'show']);
    Route::post('/store', [ScheduleController::class, 'store']);
    Route::post('/add-events', [ScheduleController::class, 'addEvents']);
});
