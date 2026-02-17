<?php

namespace Tests\Feature;

use App\Models\ComplianceDocument;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplianceTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('compliance.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('compliance.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_compliance_document(): void
    {
        $response = $this->actingAs($this->user)->post(route('compliance.store'), [
            'title' => 'Professional Engineer License',
            'type' => 'license',
            'issuing_body' => 'PEO',
            'document_number' => 'PE-12345',
            'issue_date' => '2025-01-01',
            'expiry_date' => '2027-01-01',
            'reminder_days' => 60,
            'is_critical' => true,
            'holder_id' => $this->user->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('compliance_documents', [
            'title' => 'Professional Engineer License',
            'type' => 'license',
            'issuing_body' => 'PEO',
            'status' => 'valid',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('compliance.store'), []);
        $response->assertSessionHasErrors(['title', 'type', 'expiry_date']);
    }

    public function test_store_validates_type_enum(): void
    {
        $response = $this->actingAs($this->user)->post(route('compliance.store'), [
            'title' => 'Test',
            'type' => 'invalid_type',
            'expiry_date' => '2027-01-01',
        ]);
        $response->assertSessionHasErrors('type');
    }

    public function test_show_page_loads(): void
    {
        $doc = ComplianceDocument::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'Test License',
            'type' => 'license',
            'status' => 'valid',
            'expiry_date' => '2027-01-01',
            'reminder_days' => 30,
        ]);

        $response = $this->actingAs($this->user)->get(route('compliance.show', $doc));
        $response->assertStatus(200);
    }

    public function test_edit_page_loads(): void
    {
        $doc = ComplianceDocument::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'Test License',
            'type' => 'license',
            'status' => 'valid',
            'expiry_date' => '2027-01-01',
            'reminder_days' => 30,
        ]);

        $response = $this->actingAs($this->user)->get(route('compliance.edit', $doc));
        $response->assertStatus(200);
    }

    public function test_can_update_compliance_document(): void
    {
        $doc = ComplianceDocument::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'Old Title',
            'type' => 'license',
            'status' => 'valid',
            'expiry_date' => '2027-01-01',
            'reminder_days' => 30,
        ]);

        $response = $this->actingAs($this->user)->patch(route('compliance.update', $doc), [
            'title' => 'New Title',
            'type' => 'certification',
            'expiry_date' => '2028-01-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('compliance_documents', [
            'id' => $doc->id,
            'title' => 'New Title',
            'type' => 'certification',
        ]);
    }

    public function test_can_delete_compliance_document(): void
    {
        $doc = ComplianceDocument::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'Delete Me',
            'type' => 'license',
            'status' => 'valid',
            'expiry_date' => '2027-01-01',
            'reminder_days' => 30,
        ]);

        $response = $this->actingAs($this->user)->delete(route('compliance.destroy', $doc));
        $response->assertRedirect(route('compliance.index'));
        $this->assertDatabaseMissing('compliance_documents', ['id' => $doc->id]);
    }

    public function test_is_expired_accessor(): void
    {
        $doc = new ComplianceDocument(['expiry_date' => now()->subDay()]);
        $this->assertTrue($doc->is_expired);

        $doc2 = new ComplianceDocument(['expiry_date' => now()->addYear()]);
        $this->assertFalse($doc2->is_expired);
    }

    public function test_is_expiring_soon_accessor(): void
    {
        $doc = new ComplianceDocument([
            'expiry_date' => now()->addDays(15),
            'reminder_days' => 30,
        ]);
        $this->assertTrue($doc->is_expiring_soon);

        $doc2 = new ComplianceDocument([
            'expiry_date' => now()->addDays(60),
            'reminder_days' => 30,
        ]);
        $this->assertFalse($doc2->is_expiring_soon);
    }

    public function test_computed_status_accessor(): void
    {
        $expired = new ComplianceDocument(['expiry_date' => now()->subDay(), 'reminder_days' => 30]);
        $this->assertEquals('expired', $expired->computed_status);

        $expiring = new ComplianceDocument(['expiry_date' => now()->addDays(10), 'reminder_days' => 30]);
        $this->assertEquals('expiring_soon', $expiring->computed_status);

        $valid = new ComplianceDocument(['expiry_date' => now()->addYear(), 'reminder_days' => 30]);
        $this->assertEquals('valid', $valid->computed_status);
    }

    public function test_index_filters_expired(): void
    {
        ComplianceDocument::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'Expired Doc',
            'type' => 'license',
            'status' => 'valid',
            'expiry_date' => now()->subMonth(),
            'reminder_days' => 30,
        ]);
        ComplianceDocument::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'Valid Doc',
            'type' => 'license',
            'status' => 'valid',
            'expiry_date' => now()->addYear(),
            'reminder_days' => 30,
        ]);

        $response = $this->actingAs($this->user)->get(route('compliance.index', ['status' => 'expired']));
        $response->assertStatus(200);
        $response->assertSee('Expired Doc');
        $response->assertDontSee('Valid Doc');
    }
}
