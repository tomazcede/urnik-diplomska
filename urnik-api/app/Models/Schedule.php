<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function events(){
        return $this->belongsToMany(Event::class, 'schedule_events', 'schedule_id', 'event_id');
    }

    public function convertToJson($from_date = null, $to_date = null){
        try {
            $schedule = [];

            $schedule['name'] = $this->name;
            $schedule['schedule'] = [];

            if ($from_date && $to_date) {
                $period = CarbonPeriod::create($from_date, $to_date);
                $days = [];

                foreach ($period as $date) {
                    $dayName = strtolower($date->format('D'));
                    $days[] = $dayName;
                }

                $days = array_keys($days);
            } else {
                $days = Event::AVALIABLE_DAYS;
            }

            foreach($days as $day){
                $schedule['schedule'][$day] = [];

                $eventsOnDay = $this->events()
                    ->where('day', $day)
                    ->where('start_date', '<=', $from_date ?? date('Y-m-d'))
                    ->where(function($query) use ($to_date){
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', $to_date ?? date('Y-m-d'));
                    })
                    ->orderBy('from_hour')
                    ->get();

                $schedule['schedule'][$day] = $eventsOnDay->toArray();
            }

            return $schedule;
        } catch(\Exception $e){
            return null;
        }
    }

    public function addEvents($events){
        $eventIds = [];

        foreach ($events as $eventData) {
            if (!empty($eventData['id'])) {
                $eventIds[] = $eventData['id'];
            } else {
                $event = Event::create($eventData);
                $eventIds[] = $event->id;
            }
        }

        $this->events()->syncWithoutDetaching($eventIds);
    }
}
