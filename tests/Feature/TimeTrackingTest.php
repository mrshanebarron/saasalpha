<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeTrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Tenant $tenant;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test Project',
            'client_name' => 'Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);
    }

    public function test_index_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('time-tracking.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('time-tracking.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_time_entry(): void
    {
        $response = $this->actingAs($this->user)->post(route('time-tracking.store'), [
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 4.5,
            'category' => 'engineering',
            'description' => 'Site inspection work',
            'billable' => true,
            'rate' => 150,
        ]);

        $response->assertRedirect(route('time-tracking.index'));
        $this->assertDatabaseHas('time_entries', [
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'hours' => 4.5,
            'status' => 'submitted',
            'description' => 'Site inspection work',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('time-tracking.store'), []);
        $response->assertSessionHasErrors(['project_id', 'date', 'hours', 'category', 'description']);
    }

    public function test_store_validates_hours_range(): void
    {
        $response = $this->actingAs($this->user)->post(route('time-tracking.store'), [
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 25,
            'category' => 'engineering',
            'description' => 'Too many hours',
        ]);
        $response->assertSessionHasErrors('hours');
    }

    public function test_edit_page_loads(): void
    {
        $entry = TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 4,
            'category' => 'engineering',
            'description' => 'Work',
            'status' => 'submitted',
            'rate' => 150,
        ]);

        $response = $this->actingAs($this->user)->get(route('time-tracking.edit', $entry));
        $response->assertStatus(200);
    }

    public function test_can_update_time_entry(): void
    {
        $entry = TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 4,
            'category' => 'engineering',
            'description' => 'Old description',
            'status' => 'submitted',
            'rate' => 150,
        ]);

        $response = $this->actingAs($this->user)->patch(route('time-tracking.update', $entry), [
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 6,
            'category' => 'review',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('time-tracking.index'));
        $this->assertDatabaseHas('time_entries', ['id' => $entry->id, 'hours' => 6, 'category' => 'review']);
    }

    public function test_cannot_update_approved_entry(): void
    {
        $entry = TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 4,
            'category' => 'engineering',
            'description' => 'Approved work',
            'status' => 'approved',
            'rate' => 150,
        ]);

        $response = $this->actingAs($this->user)->patch(route('time-tracking.update', $entry), [
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 8,
            'category' => 'engineering',
            'description' => 'Try to change',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('time_entries', ['id' => $entry->id, 'hours' => 4]);
    }

    public function test_can_delete_time_entry(): void
    {
        $entry = TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 4,
            'category' => 'engineering',
            'description' => 'Delete me',
            'status' => 'submitted',
            'rate' => 150,
        ]);

        $response = $this->actingAs($this->user)->delete(route('time-tracking.destroy', $entry));
        $response->assertRedirect(route('time-tracking.index'));
        $this->assertDatabaseMissing('time_entries', ['id' => $entry->id]);
    }

    public function test_cannot_delete_approved_entry(): void
    {
        $entry = TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 4,
            'category' => 'engineering',
            'description' => 'Approved',
            'status' => 'approved',
            'rate' => 150,
        ]);

        $response = $this->actingAs($this->user)->delete(route('time-tracking.destroy', $entry));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('time_entries', ['id' => $entry->id]);
    }

    public function test_can_approve_entry(): void
    {
        $entry = TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => 4,
            'category' => 'engineering',
            'description' => 'Approve me',
            'status' => 'submitted',
            'rate' => 150,
        ]);

        $response = $this->actingAs($this->user)->post(route('time-tracking.approve', $entry));
        $response->assertRedirect();
        $this->assertDatabaseHas('time_entries', [
            'id' => $entry->id,
            'status' => 'approved',
            'approved_by' => $this->user->id,
        ]);
    }

    public function test_can_bulk_approve(): void
    {
        $entries = collect([1, 2, 3])->map(fn($i) => TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'date' => '2026-02-16',
            'hours' => $i,
            'category' => 'engineering',
            'description' => "Entry $i",
            'status' => 'submitted',
            'rate' => 150,
        ]));

        $response = $this->actingAs($this->user)->post(route('time-tracking.bulk-approve'), [
            'ids' => $entries->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        foreach ($entries as $entry) {
            $this->assertDatabaseHas('time_entries', ['id' => $entry->id, 'status' => 'approved']);
        }
    }

    public function test_amount_accessor(): void
    {
        $entry = new TimeEntry(['hours' => 4.5, 'rate' => 150]);
        $this->assertEquals(675, $entry->amount);
    }
}
