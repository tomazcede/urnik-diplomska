<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show(Event $event) {
        try {
            return response()->json($event);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paginate(Request $request) {
        try {
            $query = Event::query()
                ->where('is_public', true);

            if($request->search){
                $query->whereLike('name', '%'.$request->search.'%');
            }

            if($request->faculty_id){
                $query->where('faculty_id', $request->faculty_id);
            }

            return response()->json($query->paginate($request->per_page ?? 10));
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function parseEvents(Request $request) {
        $file = $request->file('file');

        $events = Event::parseEventsFromFile($file, $request->faculty_id);

        return response()->json(compact('events'));
    }
}
