<?php

namespace App\Models;

use App\Services\ExcelParser;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'from_hour',
        'to_hour',
        'location',
        'is_public',
        'faculty_id',
        'start_date',
        'end_date',
        'day',
        'color'
    ];

    const AVALIABLE_DAYS = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }

    public function schedules(){
        return $this->belongsToMany(Schedule::class, 'schedule_events', 'event_id', 'schedule_id');
    }

    public static function parseEventsFromFile($file, $facultyId = null){
        $fileType = $file->getClientOriginalExtension();

        switch ($fileType) {
            case 'ics':
            case 'ical':
                $icalContent = file_get_contents($file->getRealPath());
                $events = \App\Services\ICalParser::parse($icalContent, $facultyId);
                break;
            case 'xls':
                if(!$facultyId){
                    throw new \Exception('Faculty ID not set');
                }
                $events = ExcelParser::parse($file, $facultyId);
                break;
            default:
                throw new \Exception('File type not supported');
        }

        return $events;
    }
}
