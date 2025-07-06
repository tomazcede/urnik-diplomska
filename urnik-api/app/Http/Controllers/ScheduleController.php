<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    public function show(Request $request) {
        try {
            $schedule = $request->id && $request->id != null ? Schedule::find($request->id) : Schedule::convertFromJson($request->json);

            if($request->from && $request->to){
                return response()->json($schedule->convertToJson($request->from, $request->to));
            }

            return response()->json($schedule->convertToJson());
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request) {
        try {
            $validate = $request->validate([
                'name' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $schedule = Schedule::create($validate);

            if($request->events){
                $schedule->addEvents($request->events);
            }

            return response()->json($schedule->convertToJson());
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function addEvents(Request $request) {
        try {
            if($request->json) {
                $schedule = Schedule::convertFromJson($request->json);
                $schedule->addEventsToJson($request->events);
            } else {
                $schedule = Schedule::find($request->id);
                $schedule->addEvents($request->events);
            }

            return response()->json($schedule->convertToJson());
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request) {
        try {
            if($request->json) {
                $schedule = Schedule::convertFromJson($request->json);
            } else {
                $schedule = Schedule::find($request->id);
            }

            $data = Schedule::generateIcal($schedule);

            $filename = 'schedule_' . Str::random(10) . '.ics';
            $path = storage_path('app/' . $filename);

            file_put_contents($path, $data);

            return response()->download($path)->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
