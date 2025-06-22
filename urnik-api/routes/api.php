<?php

use App\Http\Controllers\ScheduleController;
use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'schedule'], function () {
    Route::post('/show/{schedule}', [ScheduleController::class, 'show']);
    Route::post('/store', [ScheduleController::class, 'store']);
    Route::post('/add-events', [ScheduleController::class, 'addEvents']);

    Route::post('/convert-from', function (Request $request) {
        $scheduleData = json_decode($request->json);

        $schedule = new Schedule();
        $schedule->name = $scheduleData->name;

        $events = collect();

        foreach ($scheduleData->schedule as $day) {
            foreach ($day as $hour) {
                foreach ($hour as $event) {
                    $data = collect($event);
                    $e = new Event($data->toArray());
                    $events->push($e);
                }
            }
        }

        $schedule->setRelation('events', $events);

        return response()->json($schedule->co());

    });
});
