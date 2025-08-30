<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ScheduleControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_show_schedule_by_id()
    {
        $user = User::factory()->create();
        $schedule = Schedule::factory()->create(["user_id" => $user->id]);

        $response = $this->json('POST', '/api/schedule/show', ['id' => $schedule->id]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $schedule->name]);
    }

    /** @test */
    public function test_empty_json_on_show_schedule()
    {
        $response = $this->json('POST', '/api/schedule/show', ['json' => '']);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'new']);
    }

    /** @test */
    public function test_updating_schedule()
    {
        $user = User::factory()->create();
        $schedule = Schedule::factory()->create(["user_id" => $user->id]);

        $payload = [
            'id' => $schedule->id,
            'name' => 'Updated Schedule',
            'primary_color' => '#FF0000',
        ];

        $response = $this->json('POST', '/api/schedule/update', $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Schedule']);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'name' => 'Updated Schedule',
            'primary_color' => '#FF0000',
        ]);
    }

    /** @test */
    public function test_adding_events_to_schedule()
    {
        $user = User::factory()->create();
        $schedule = Schedule::factory()->create(["user_id" => $user->id]);

        $events = [
            [
                'name' => 'test1',
                'from_hour' => '10',
                'to_hour' => '12',
                'location' => '',
                'is_public' => true,
                'faculty_id' => null,
                'start_date' => '2025-01-01',
                'end_date' => '2025-07-10',
                'day' => 'mon',
                'color' => '#FF0000',
            ]
        ];

        $response = $this->json('POST', '/api/schedule/add-events', [
            'id' => $schedule->id,
            'events' => $events,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'test1']);
    }

    /** @test */
    public function test_removing_events_from_schedule()
    {
        $user = User::factory()->create();
        $schedule = Schedule::factory()->create(["user_id" => $user->id]);

        $event = $schedule->events()->create([
            'name' => 'test1',
            'from_hour' => '10',
            'to_hour' => '12',
            'location' => '',
            'is_public' => true,
            'faculty_id' => null,
            'start_date' => '2025-01-01',
            'end_date' => '2025-07-10',
            'day' => 'mon',
            'color' => '#FF0000',
        ]);

        $response = $this->json('POST', '/api/schedule/remove-event', [
            'id' => $schedule->id,
            'event_id' => $event->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonMissing(['name' => 'test1']);
    }
}
