<?php

namespace App\Services;

use Carbon\Carbon;

class ICalParser
{
    public static function parse($icalContent, $faculty_id = null)
    {
        $lines = preg_split('/\R/', $icalContent);
        $events = [];
        $event = [];
        $insideEvent = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $insideEvent = true;
                $event = [];
                continue;
            }

            if ($line === 'END:VEVENT') {
                if (!empty($event)) {
                    $events[] = self::mapEvent($event, $faculty_id);
                }
                $insideEvent = false;
                continue;
            }

            if ($insideEvent) {
                if (strpos($line, ':') !== false) {
                    [$key, $value] = explode(':', $line, 2);
                    $event[$key] = $value;
                }
            }
        }

        return $events;
    }

    protected static function mapEvent($event, $faculty_id = null)
    {
        $from = self::parseDateTime($event['DTSTART;TZID=Europe/Ljubljana;VALUE=DATE-TIME'] ?? null);
        $to = self::parseDateTime($event['DTEND;TZID=Europe/Ljubljana;VALUE=DATE-TIME'] ?? null);
        $until = self::parseUntilDate($event['RRULE'] ?? null);

        return [
            'name'       => $event['SUMMARY'] ?? null,
            'from_hour'  => $from ? $from->hour : null,
            'to_hour'    => $to ? $to->hour : null,
            'location'   => $event['LOCATION'] ?? null,
            'is_public'  => $faculty_id ? true : false,
            'faculty_id' => $faculty_id,
            'start_date' => now()->format('Y-m-d'),
            'end_date'   => $until ? $until->format('Y-m-d') : null,
            'day'        => $from ? strtolower($from->format('D')) : null,
        ];
    }

    protected static function parseDateTime($dateTimeString)
    {
        if (!$dateTimeString) {
            return null;
        }

        return Carbon::createFromFormat('Ymd\THis', $dateTimeString);
    }

    protected static function parseUntilDate($rrule)
    {
        if (!$rrule) {
            return null;
        }
        if (preg_match('/UNTIL=(\d{8}T\d{6})/', $rrule, $matches)) {
            return \DateTime::createFromFormat('Ymd\THis', $matches[1]);
        }
        return null;
    }
}
