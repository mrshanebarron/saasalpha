<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Quote;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;
    private User $manager;
    private User $staff;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->create(['tenant_id' => $this->tenant->id, 'role' => 'admin']);
        $this->manager = User::factory()->create(['tenant_id' => $this->tenant->id, 'role' => 'manager']);
        $this->staff = User::factory()->create(['tenant_id' => $this->tenant->id, 'role' => 'staff']);
        $this->project = Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Test Project',
            'client_name' => 'Test Client',
            'status' => 'active',
        ]);
    }

    public function test_staff_cannot_access_enquiry_create(): void
    {
        $response = $this->actingAs($this->staff)->get(route('enquiries.create'));
        $response->assertStatus(403);
    }

    public function test_manager_can_access_enquiry_create(): void
    {
        $response = $this->actingAs($this->manager)->get(route('enquiries.create'));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_enquiry_create(): void
    {
        $response = $this->actingAs($this->admin)->get(route('enquiries.create'));
        $response->assertStatus(200);
    }

    public function test_staff_cannot_store_enquiry(): void
    {
        $response = $this->actingAs($this->staff)->post(route('enquiries.store'), [
            'client_name' => 'Test',
            'source' => 'referral',
            'priority' => 'normal',
        ]);
        $response->assertStatus(403);
    }

    public function test_staff_can_view_enquiry_index(): void
    {
        $response = $this->actingAs($this->staff)->get(route('enquiries.index'));
        $response->assertStatus(200);
    }

    public function test_staff_can_view_enquiry_show(): void
    {
        $enquiry = Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Test',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->staff)->get(route('enquiries.show', $enquiry));
        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_quote_create(): void
    {
        $response = $this->actingAs($this->staff)->get(route('quotes.create'));
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_project_create(): void
    {
        $response = $this->actingAs($this->staff)->get(route('projects.create'));
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_compliance_create(): void
    {
        $response = $this->actingAs($this->staff)->get(route('compliance.create'));
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_subcontractor_create(): void
    {
        $response = $this->actingAs($this->staff)->get(route('subcontractors.create'));
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_document_create(): void
    {
        $response = $this->actingAs($this->staff)->get(route('documents.create'));
        $response->assertStatus(403);
    }

    public function test_staff_cannot_delete_enquiry(): void
    {
        $enquiry = Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Test',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->staff)->delete(route('enquiries.destroy', $enquiry));
        $response->assertStatus(403);
        $this->assertDatabaseHas('enquiries', ['id' => $enquiry->id]);
    }

    public function test_staff_cannot_approve_time_entry(): void
    {
        $timeEntry = \App\Models\TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->staff->id,
            'project_id' => $this->project->id,
            'date' => now()->format('Y-m-d'),
            'hours' => 8,
            'description' => 'Test entry',
            'category' => 'engineering',
            'rate' => 150,
            'billable' => true,
        ]);

        $response = $this->actingAs($this->staff)->post(route('time-tracking.approve', $timeEntry));
        $response->assertStatus(403);
    }

    public function test_manager_can_approve_time_entry(): void
    {
        $timeEntry = \App\Models\TimeEntry::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->staff->id,
            'project_id' => $this->project->id,
            'date' => now()->format('Y-m-d'),
            'hours' => 8,
            'description' => 'Test entry',
            'category' => 'engineering',
            'rate' => 150,
            'billable' => true,
        ]);

        $response = $this->actingAs($this->manager)->post(route('time-tracking.approve', $timeEntry));
        $response->assertRedirect();
    }

    public function test_only_admin_can_access_audit(): void
    {
        $this->actingAs($this->admin)->get(route('audit.index'))->assertStatus(200);
        $this->actingAs($this->manager)->get(route('audit.index'))->assertStatus(403);
        $this->actingAs($this->staff)->get(route('audit.index'))->assertStatus(403);
    }

    public function test_staff_can_manage_own_time_entries(): void
    {
        $this->actingAs($this->staff)->get(route('time-tracking.create'))->assertStatus(200);

        $response = $this->actingAs($this->staff)->post(route('time-tracking.store'), [
            'project_id' => $this->project->id,
            'date' => now()->format('Y-m-d'),
            'hours' => 4,
            'description' => 'Staff work',
            'category' => 'engineering',
            'rate' => 100,
            'billable' => true,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('time_entries', ['description' => 'Staff work']);
    }

    public function test_staff_can_manage_own_cpd(): void
    {
        $this->actingAs($this->staff)->get(route('cpd.create'))->assertStatus(200);

        $response = $this->actingAs($this->staff)->post(route('cpd.store'), [
            'title' => 'Staff CPD',
            'category' => 'course',
            'provider' => 'Test Provider',
            'hours' => 4,
            'date_completed' => now()->format('Y-m-d'),
        ]);
        $response->assertRedirect();
    }
}
