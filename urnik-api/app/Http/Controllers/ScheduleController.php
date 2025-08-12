<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    public function show(Request $request) {
        try {
            $schedule = $request->id && $request->id != null ? Schedule::find($request->id) : Schedule::convertFromJson($request->json);

            return $request->from && $request->to ?
                response()->json($schedule->convertToJson($request->from, $request->to))
                :
                response()->json($schedule->convertToJson());
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

//            if(auth()->user()->id !== $validate['user_id'])
//                return response("Action prohibited", 403);

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
//
//                if(auth()->user()->id !== $schedule->user_id)
//                    return response("Action prohibited", 403);

                $schedule->addEvents($request->events);
            }

            return response()->json($schedule->convertToJson());
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeEvent(Request $request) {
        try {
            if($request->json) {
                $schedule = Schedule::convertFromJson($request->json);
                $schedule->removeEventFromJson($request->event_id);
            } else {
                $schedule = Schedule::find($request->id);
//                if(auth()->user()->id !== $schedule->user_id)
//                    return response("Action prohibited", 403);

                $schedule->removeEvent($request->event_id);
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
