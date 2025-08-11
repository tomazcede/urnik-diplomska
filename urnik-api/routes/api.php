<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'user'], function () {
    Route::post('/show/{user}', [UserController::class, 'show']);
    Route::post('/store/{user}', [UserController::class, 'store']);
    Route::post('/delete/{user}', [UserController::class, 'delete']);
});

Route::group(['prefix' => 'auth', 'middleware' => 'web'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/current', [UserController::class, 'getCurrent']);
});

Route::group(['prefix' => 'schedule'], function () {
    Route::post('/show', [ScheduleController::class, 'show']);
    Route::post('/store', [ScheduleController::class, 'store']);
    Route::post('/add-events', [ScheduleController::class, 'addEvents']);
    Route::post('/remove-event', [ScheduleController::class, 'removeEvent']);
    Route::post('/export', [ScheduleController::class, 'export']);
});

Route::group(['prefix' => 'event'], function () {
    Route::post('/show/{event}', [EventController::class, 'show']);
    Route::post('/paginate', [EventController::class, 'paginate']);
    Route::post('/parse', [EventController::class, 'parseEvents']);
});

Route::group(['prefix' => 'faculty'], function () {
    Route::post('/all', [FacultyController::class, 'all']);
});
