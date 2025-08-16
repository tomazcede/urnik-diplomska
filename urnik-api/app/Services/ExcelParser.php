<?php

namespace App\Services;

use App\Models\Faculty;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelParser
{
    public static function parse($file, $faculty_id) {
        $faculty = Faculty::find($faculty_id);

        if(!$faculty) {
            throw new \Exception('Faculty not found');
        }

        switch ($faculty->name) {
            case "Pedagoška fakulteta":
                $events = self::pedagoskaParser($file, $faculty_id);
                break;
            default:
                throw new \Exception('Faculty not supported');
        }

        return $events;
    }

    public static function pedagoskaParser($file, $faculty_id) {
        $events = [];
        $htmlContent = file_get_contents($file->getRealPath());
        $rows = self::convertFromHtmlPed($htmlContent);

        foreach ($rows as $eventData) {
            $from = Carbon::parse($eventData['Začetek'] . " " . $eventData['Datum']);
            $to = Carbon::parse($eventData['Zaključek'] . " " . $eventData['Datum']);

            $events[] = [
                'name'       => $eventData['Opis'] ?? null,
                'from_hour'  => $from ? $from->hour : null,
                'to_hour'    => $to ? $to->hour : null,
                'location'   => $eventData['Predavalnica'] ?? null,
                'is_public'  => true,
                'faculty_id' => $faculty_id,
                'start_date' => now()->format('Y-m-d'),
                'end_date'   => null,
                'day'        => $from ? strtolower($from->format('D')) : null,
            ];
        }

        return $events;
    }

    private static function convertFromHtmlPed($html)
    {
        $uniqueFields = ['Začetek', 'Zaključek', 'Predavalnica', 'Aktivnost', 'Opis'];

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $rows = [];
        foreach ($dom->getElementsByTagName('tr') as $tr) {
            $cells = [];
            foreach ($tr->getElementsByTagName('td') as $td) {
                $cells[] = trim($td->textContent);
            }
            if ($cells) {
                $rows[] = $cells;
            }
        }

        if (empty($rows)) {
            return collect();
        }

        $headers = array_map(fn($h) => $h ?: 'column_' . uniqid(), $rows[0]);

        $dataRows = array_slice($rows, 1);
        $collection = collect($dataRows)->map(function ($row) use ($headers) {
            return collect($headers)
                ->combine($row)
                ->toArray();
        });

        return $collection->unique(function ($row) use ($uniqueFields) {
            return implode('|', array_map(fn($field) => $row[$field] ?? '', $uniqueFields));
        })->values();
    }
}
