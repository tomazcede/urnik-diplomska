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
            $schedule['min_hour'] = 7;
            $schedule['max_hour'] = 15;

            if ($from_date && $to_date) {
                $period = CarbonPeriod::create($from_date, $to_date);
                $days = [];

                foreach ($period as $date) {
                    $dayName = strtolower($date->format('D'));
                    $days[] = $dayName;
                }
            } else {
                $days = Event::AVALIABLE_DAYS;
            }

            foreach ($days as $day) {
                $query = $this->events()
                    ->where('day', $day)
                    ->where('start_date', '<=', $from_date ?? date('Y-m-d'))
                    ->where(function ($query) use ($to_date) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', $to_date ?? date('Y-m-d'));
                    })->orderBy('from_hour')->get();

                if($query->count() <= 0)
                    $schedule['schedule'][$day] = [];

                $minHour = $query->min('from_hour');
                if($minHour < $schedule['min_hour']){
                    $schedule['min_hour'] = $minHour;
                }
                $maxHour = $query->max('to_hour');
                if($maxHour > $schedule['max_hour']){
                    $schedule['max_hour'] = $maxHour;
                }

                $hourly = [];

                while($minHour <= $maxHour){
                    $hourly[$minHour] = $query
                        ->filter(fn($event) =>
                            $event->from_hour <= $minHour && $event->to_hour >= $minHour
                        )
                        ->values()
                        ->toArray();

                    $minHour += 1;
                }

                $schedule['schedule'][$day] = $hourly;
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
