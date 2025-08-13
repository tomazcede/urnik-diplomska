<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use MongoDB\BSON\Int64;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'primary_color',
        'secondary_color',
        'background_color',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function events(){
        return $this->belongsToMany(\App\Models\Event::class, 'schedule_events', 'schedule_id', 'event_id');
    }

    public function convertToJson($from_date = null, $to_date = null){
        try {
            $schedule = [];

            $schedule['name'] = $this->name;
            $schedule['primary_color'] = $this->primary_color;
            $schedule['secondary_color'] = $this->secondary_color;
            $schedule['background_color'] = $this->background_color;
            $schedule['schedule'] = [];
            $schedule['min_hour'] = 7;
            $schedule['max_hour'] = 15;

            if ($from_date && $to_date) {
                $period = CarbonPeriod::create($from_date, $to_date);
                if($period->count() > 7) throw new \Exception("More than 7 days");
                $days = [];

                foreach ($period as $date) {
                    $dayName = strtolower($date->format('D'));
                    $days[] = $dayName;
                }
            } else {
                $days = \App\Models\Event::AVALIABLE_DAYS;
            }

            foreach ($days as $day) {
                $query = collect($this->events)->filter(function ($event) use ($day, $from_date, $to_date) {
                    return $event->day === $day &&
                        ($event->start_date <= ($from_date || Carbon::now()) ) &&
                        (($to_date && $event->end_date >= $to_date) || !$to_date || $event->end_date === null);
                })->sortBy('from_hour');

                if($query->count() <= 0)
                    $schedule['schedule'][$day] = [];

                $minHour = $query->min('from_hour');
                if($minHour !== null && $minHour < $schedule['min_hour']){
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

    public static function convertFromJson($scheduleJson){
        $scheduleData = json_decode($scheduleJson);

        $schedule = new Schedule();
        $events = collect();

        if($scheduleData == null){
            $schedule->setRelation('events', $events->unique());
            $schedule->name = 'new';

            return $schedule;
        }

        $schedule->name = $scheduleData->name;
        $ids = collect();

        foreach ($scheduleData->schedule as $day) {
            foreach ($day as $hour) {
                foreach ($hour as $event) {
                    if($events->where('name', $event->name)
                            ->where('from_hour', $event->from_hour)
                            ->where('to_hour', $event->to_hour)
                            ->where('day', $event->day)
                            ->where('start_date', $event->start_date)
                            ->first() !== null){
                        continue;
                    }

                    $data = collect($event);
                    $e = new \App\Models\Event($data->toArray());

                    if(isset($data['eid']))
                        $e->eid = $data['eid'];
                    else{
                        do{
                            $e->eid = rand(10000, 99999);
                        } while($ids->contains($e->eid));
                    }

                    $ids->push($e->eid);

                    $events->push($e);
                }
            }
        }

        $schedule->setRelation('events', $events->unique());

        return $schedule;

    }

    public function addEvents($events){
        $eventIds = [];

        foreach ($events as $eventData) {
            if (!empty($eventData['id'])) {
                $eventIds[] = $eventData['id'];
            } else {
                $event = \App\Models\Event::create($eventData);
                $eventIds[] = $event->id;
            }
        }

        $this->events()->syncWithoutDetaching($eventIds);
    }

    public function addEventsToJson($events){
        $ids = collect($this->events)->pluck('eid');
        foreach ($events as $event) {
            $data = collect($event)->toArray();
            $e = new \App\Models\Event($data);
            do{
                $e->eid = rand(10000, 99999);
            } while($ids->contains($e->eid));
            $ids->push($e->eid);
            $this->events->push($e);
        }
    }

    public function removeEvent($eventId){
        $this->events()->detach($eventId);
    }

    public function removeEventFromJson($eid){
        $this->events = $this->events->filter(function ($event) use ($eid){
            return $event->eid != $eid;
        });
    }

    public function updateJsonEvent($data){
        $this->removeEventFromJson($data['eid']);

        $this->addEventsToJson([$data]);
    }

    public static function generateIcal(Schedule $schedule){
        $calendar = Calendar::create($schedule->name);
        $events = [];

        foreach($schedule->events as $event){
            $e = json_decode(json_encode($event));

            $rrule = $event->end_date ?
                RRule::frequency(RecurrenceFrequency::Weekly)->until(new DateTime($e->end_date))
                :
                RRule::frequency(RecurrenceFrequency::Weekly);

            $events[] = Event::create($e->name)
                ->startsAt(new DateTime($e->start_date.' '.$e->from_hour.':00'))
                ->endsAt(new DateTime($e->start_date.' '.$e->to_hour.':00'))
                ->rrule($rrule);
        }

        return $calendar->event($events)->toString();
    }
}
