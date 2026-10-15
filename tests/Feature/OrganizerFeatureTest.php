<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizerFeatureTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Life Dashboard');
    }

    public function test_tasks_page_renders_and_task_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.index'));
        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->user)->post(route('tasks.store'), [
            'title'       => 'Test New Task',
            'priority'    => 'urgent',
            'status'      => 'pending',
            'due_date'    => today()->toDateString(),
            'description' => 'A test description',
        ]);

        $createResponse->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title'   => 'Test New Task',
        ]);
    }

    public function test_calendar_page_renders_and_event_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->get(route('calendar.index'));
        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->user)->post(route('events.store'), [
            'title'          => 'Client Meeting',
            'category'       => 'work',
            'start_datetime' => now()->addHour()->toDateTimeString(),
            'end_datetime'   => now()->addHours(2)->toDateTimeString(),
        ]);

        $createResponse->assertRedirect(route('calendar.index'));
        $this->assertDatabaseHas('events', [
            'user_id' => $this->user->id,
            'title'   => 'Client Meeting',
        ]);
    }

    public function test_notes_page_renders_and_note_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->get(route('notes.index'));
        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->user)->post(route('notes.store'), [
            'title'   => 'Quick Idea',
            'content' => 'Explore AI prompts',
            'color'   => '#7c3aed',
        ]);

        $createResponse->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('notes', [
            'user_id' => $this->user->id,
            'title'   => 'Quick Idea',
        ]);
    }
}
