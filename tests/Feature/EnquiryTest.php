<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnquiryTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('enquiries.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('enquiries.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_enquiry(): void
    {
        $response = $this->actingAs($this->user)->post(route('enquiries.store'), [
            'client_name' => 'John Smith',
            'client_company' => 'Smith Engineering',
            'client_email' => 'john@smith.com',
            'source' => 'referral',
            'priority' => 'normal',
            'estimated_value' => 50000,
            'description' => 'New bridge project assessment',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', [
            'client_name' => 'John Smith',
            'client_company' => 'Smith Engineering',
            'status' => 'new',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_store_generates_reference(): void
    {
        $this->actingAs($this->user)->post(route('enquiries.store'), [
            'client_name' => 'Test Client',
            'source' => 'website',
            'priority' => 'high',
        ]);

        $this->assertDatabaseHas('enquiries', ['reference' => 'ENQ-0001']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('enquiries.store'), []);
        $response->assertSessionHasErrors(['client_name', 'source', 'priority']);
    }

    public function test_store_validates_source_enum(): void
    {
        $response = $this->actingAs($this->user)->post(route('enquiries.store'), [
            'client_name' => 'Test',
            'source' => 'invalid_source',
            'priority' => 'normal',
        ]);
        $response->assertSessionHasErrors('source');
    }

    public function test_show_page_loads(): void
    {
        $enquiry = Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Test',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)->get(route('enquiries.show', $enquiry));
        $response->assertStatus(200);
    }

    public function test_edit_page_loads(): void
    {
        $enquiry = Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Test',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)->get(route('enquiries.edit', $enquiry));
        $response->assertStatus(200);
    }

    public function test_can_update_enquiry(): void
    {
        $enquiry = Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Old Name',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)->put(route('enquiries.update', $enquiry), [
            'client_name' => 'New Name',
            'source' => 'website',
            'priority' => 'high',
            'status' => 'reviewing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'client_name' => 'New Name',
            'status' => 'reviewing',
        ]);
    }

    public function test_can_delete_enquiry(): void
    {
        $enquiry = Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Delete Me',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)->delete(route('enquiries.destroy', $enquiry));
        $response->assertRedirect(route('enquiries.index'));
        $this->assertDatabaseMissing('enquiries', ['id' => $enquiry->id]);
    }

    public function test_index_filters_by_status(): void
    {
        Enquiry::create(['tenant_id' => $this->tenant->id, 'reference' => 'ENQ-0001', 'client_name' => 'New One', 'source' => 'referral', 'priority' => 'normal', 'status' => 'new']);
        Enquiry::create(['tenant_id' => $this->tenant->id, 'reference' => 'ENQ-0002', 'client_name' => 'Reviewing One', 'source' => 'referral', 'priority' => 'normal', 'status' => 'reviewing']);

        $response = $this->actingAs($this->user)->get(route('enquiries.index', ['status' => 'new']));
        $response->assertStatus(200);
        $response->assertSee('New One');
        $response->assertDontSee('Reviewing One');
    }

    public function test_index_searches_by_name(): void
    {
        Enquiry::create(['tenant_id' => $this->tenant->id, 'reference' => 'ENQ-0001', 'client_name' => 'Alpha Corp', 'source' => 'referral', 'priority' => 'normal', 'status' => 'new']);
        Enquiry::create(['tenant_id' => $this->tenant->id, 'reference' => 'ENQ-0002', 'client_name' => 'Beta LLC', 'source' => 'referral', 'priority' => 'normal', 'status' => 'new']);

        $response = $this->actingAs($this->user)->get(route('enquiries.index', ['search' => 'Alpha']));
        $response->assertStatus(200);
        $response->assertSee('Alpha Corp');
        $response->assertDontSee('Beta LLC');
    }

    public function test_creates_audit_log_on_store(): void
    {
        $this->actingAs($this->user)->post(route('enquiries.store'), [
            'client_name' => 'Audited Client',
            'source' => 'referral',
            'priority' => 'normal',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'model_type' => 'Enquiry',
            'user_id' => $this->user->id,
        ]);
    }
}
