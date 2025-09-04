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

    /** @test */
    public function test_show_schedule_with_nonexistent_id()
    {
        $response = $this->json('POST', '/api/schedule/show', ['id' => 999999]);

        $response->assertStatus(404);
    }

    /** @test */
    public function test_update_schedule_with_invalid_color()
    {
        $user = User::factory()->create();
        $schedule = Schedule::factory()->create(["user_id" => $user->id]);

        $payload = [
            'id' => $schedule->id,
            'name' => 'Schedule with Bad Color',
            'primary_color' => 'not-a-color',
        ];

        $response = $this->json('POST', '/api/schedule/update', $payload);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_add_events_with_missing_fields()
    {
        $user = User::factory()->create();
        $schedule = Schedule::factory()->create(["user_id" => $user->id]);

        $events = [
            [
                'from_hour' => '09',
                'to_hour' => '11',
            ]
        ];

        $response = $this->json('POST', '/api/schedule/add-events', [
            'id' => $schedule->id,
            'events' => $events,
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['events.0.name', 'events.0.day']);
    }

    /** @test */
    public function test_remove_event_that_does_not_belong_to_schedule()
    {
        $user = User::factory()->create();
        $schedule1 = Schedule::factory()->create(["user_id" => $user->id]);
        $schedule2 = Schedule::factory()->create(["user_id" => $user->id]);

        $event = $schedule2->events()->create([
            'name' => 'foreign-event',
            'from_hour' => '08',
            'to_hour' => '10',
            'location' => '',
            'is_public' => true,
            'faculty_id' => null,
            'start_date' => '2025-01-01',
            'end_date' => '2025-07-10',
            'day' => 'tue',
            'color' => '#00FF00',
        ]);

        $response = $this->json('POST', '/api/schedule/remove-event', [
            'id' => $schedule1->id,
            'event_id' => $event->id,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function test_update_schedule_with_empty_payload()
    {
        $response = $this->json('POST', '/api/schedule/update', []);

        $response->assertStatus(422);
    }
}
