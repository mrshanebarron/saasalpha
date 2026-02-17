<?php

namespace Tests\Feature;

use App\Models\Subcontractor;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubcontractorTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('subcontractors.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('subcontractors.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_subcontractor(): void
    {
        $response = $this->actingAs($this->user)->post(route('subcontractors.store'), [
            'company_name' => 'Steel Solutions Inc',
            'contact_name' => 'Bob Builder',
            'email' => 'bob@steelsolutions.com',
            'phone' => '416-555-1234',
            'specialty' => 'Structural Steel',
            'default_rate' => 95,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subcontractors', [
            'company_name' => 'Steel Solutions Inc',
            'status' => 'active',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('subcontractors.store'), []);
        $response->assertSessionHasErrors('company_name');
    }

    public function test_show_page_loads(): void
    {
        $sub = Subcontractor::create([
            'tenant_id' => $this->tenant->id,
            'company_name' => 'Test Co',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('subcontractors.show', $sub));
        $response->assertStatus(200);
    }

    public function test_edit_page_loads(): void
    {
        $sub = Subcontractor::create([
            'tenant_id' => $this->tenant->id,
            'company_name' => 'Test Co',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('subcontractors.edit', $sub));
        $response->assertStatus(200);
    }

    public function test_can_update_subcontractor(): void
    {
        $sub = Subcontractor::create([
            'tenant_id' => $this->tenant->id,
            'company_name' => 'Old Name',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->put(route('subcontractors.update', $sub), [
            'company_name' => 'New Name',
            'specialty' => 'Concrete',
            'status' => 'inactive',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subcontractors', [
            'id' => $sub->id,
            'company_name' => 'New Name',
            'status' => 'inactive',
        ]);
    }

    public function test_can_delete_subcontractor(): void
    {
        $sub = Subcontractor::create([
            'tenant_id' => $this->tenant->id,
            'company_name' => 'Delete Me',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->delete(route('subcontractors.destroy', $sub));
        $response->assertRedirect(route('subcontractors.index'));
        $this->assertDatabaseMissing('subcontractors', ['id' => $sub->id]);
    }

    public function test_index_searches_by_company_name(): void
    {
        Subcontractor::create(['tenant_id' => $this->tenant->id, 'company_name' => 'Alpha Steel', 'status' => 'active']);
        Subcontractor::create(['tenant_id' => $this->tenant->id, 'company_name' => 'Beta Concrete', 'status' => 'active']);

        $response = $this->actingAs($this->user)->get(route('subcontractors.index', ['search' => 'Alpha']));
        $response->assertStatus(200);
        $response->assertSee('Alpha Steel');
        $response->assertDontSee('Beta Concrete');
    }
}
