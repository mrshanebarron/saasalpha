<?php

namespace Tests\Feature;

use App\Models\Deliverable;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    }

    public function test_index_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('projects.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('projects.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_project(): void
    {
        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'name' => 'Bridge Assessment',
            'client_name' => 'City of Toronto',
            'project_type' => 'structural',
            'budget' => 50000,
            'start_date' => '2026-03-01',
            'target_date' => '2026-06-01',
            'project_manager_id' => $this->user->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'Bridge Assessment',
            'client_name' => 'City of Toronto',
            'status' => 'active',
            'reference' => 'PRJ-0001',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('projects.store'), []);
        $response->assertSessionHasErrors(['name', 'client_name', 'start_date', 'project_manager_id']);
    }

    public function test_show_page_loads(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test Project',
            'client_name' => 'Test Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.show', $project));
        $response->assertStatus(200);
    }

    public function test_edit_page_loads(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test Project',
            'client_name' => 'Test Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.edit', $project));
        $response->assertStatus(200);
    }

    public function test_can_update_project(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Old Name',
            'client_name' => 'Old Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('projects.update', $project), [
            'name' => 'New Name',
            'client_name' => 'New Client',
            'status' => 'active',
            'budget' => 75000,
            'progress' => 25,
            'start_date' => '2026-03-01',
            'project_manager_id' => $this->user->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'New Name', 'progress' => 25]);
    }

    public function test_completing_project_sets_completed_date(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Complete Me',
            'client_name' => 'Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 100,
            'project_manager_id' => $this->user->id,
            'start_date' => '2026-01-01',
        ]);

        $this->actingAs($this->user)->put(route('projects.update', $project), [
            'name' => 'Complete Me',
            'client_name' => 'Client',
            'status' => 'completed',
            'start_date' => '2026-01-01',
            'project_manager_id' => $this->user->id,
        ]);

        $this->assertNotNull($project->fresh()->completed_date);
    }

    public function test_can_delete_project(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Delete Me',
            'client_name' => 'Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('projects.destroy', $project));
        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_can_add_deliverable(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test',
            'client_name' => 'Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('projects.deliverables.store', $project), [
            'title' => 'Structural Report',
            'type' => 'report',
            'due_date' => '2026-04-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('deliverables', [
            'project_id' => $project->id,
            'title' => 'Structural Report',
            'status' => 'pending',
        ]);
    }

    public function test_can_update_deliverable_status(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test',
            'client_name' => 'Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $deliverable = Deliverable::create([
            'project_id' => $project->id,
            'tenant_id' => $this->tenant->id,
            'title' => 'Report',
            'type' => 'report',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->patch(
            route('projects.deliverables.update', [$project, $deliverable]),
            ['status' => 'approved']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('deliverables', ['id' => $deliverable->id, 'status' => 'approved']);
        $this->assertNotNull($deliverable->fresh()->delivered_date);
    }

    public function test_can_add_team_member(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test',
            'client_name' => 'Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $member = User::factory()->create(['tenant_id' => $this->tenant->id, 'role' => 'staff']);

        $response = $this->actingAs($this->user)->post(route('projects.members.store', $project), [
            'user_id' => $member->id,
            'role' => 'Engineer',
            'hourly_rate' => 125,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('project_members', [
            'project_id' => $project->id,
            'user_id' => $member->id,
            'role' => 'Engineer',
        ]);
    }

    public function test_can_remove_team_member(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test',
            'client_name' => 'Client',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $pm = ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id,
            'role' => 'Manager',
        ]);

        $response = $this->actingAs($this->user)->delete(route('projects.members.destroy', [$project, $pm]));
        $response->assertRedirect();
        $this->assertDatabaseMissing('project_members', ['id' => $pm->id]);
    }

    public function test_budget_used_percent_accessor(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Budget Test',
            'client_name' => 'Client',
            'status' => 'active',
            'budget' => 10000,
            'spent' => 2500,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $this->assertEquals(25, $project->budget_used_percent);
    }

    public function test_budget_used_percent_zero_budget(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'No Budget',
            'client_name' => 'Client',
            'status' => 'active',
            'budget' => 0,
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $this->assertEquals(0, $project->budget_used_percent);
    }
}
