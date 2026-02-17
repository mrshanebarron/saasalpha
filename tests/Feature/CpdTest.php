<?php

namespace Tests\Feature;

use App\Models\CpdRecord;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CpdTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('cpd.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('cpd.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_cpd_record(): void
    {
        $response = $this->actingAs($this->user)->post(route('cpd.store'), [
            'user_id' => $this->user->id,
            'title' => 'Advanced Structural Analysis Course',
            'category' => 'course',
            'provider' => 'University of Toronto',
            'hours' => 40,
            'completed_date' => '2026-02-01',
            'certificate_number' => 'CERT-001',
            'description' => 'Graduate-level structural analysis',
        ]);

        $response->assertRedirect(route('cpd.index'));
        $this->assertDatabaseHas('cpd_records', [
            'title' => 'Advanced Structural Analysis Course',
            'category' => 'course',
            'verified' => false,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('cpd.store'), []);
        $response->assertSessionHasErrors(['user_id', 'title', 'category', 'hours', 'completed_date']);
    }

    public function test_store_validates_category_enum(): void
    {
        $response = $this->actingAs($this->user)->post(route('cpd.store'), [
            'user_id' => $this->user->id,
            'title' => 'Test',
            'category' => 'invalid',
            'hours' => 5,
            'completed_date' => '2026-02-01',
        ]);
        $response->assertSessionHasErrors('category');
    }

    public function test_edit_page_loads(): void
    {
        $record = CpdRecord::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'title' => 'Test Course',
            'category' => 'course',
            'hours' => 10,
            'completed_date' => '2026-02-01',
            'verified' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('cpd.edit', $record));
        $response->assertStatus(200);
    }

    public function test_can_update_cpd_record(): void
    {
        $record = CpdRecord::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'title' => 'Old Title',
            'category' => 'course',
            'hours' => 10,
            'completed_date' => '2026-02-01',
            'verified' => false,
        ]);

        $response = $this->actingAs($this->user)->patch(route('cpd.update', $record), [
            'title' => 'New Title',
            'category' => 'seminar',
            'hours' => 20,
            'completed_date' => '2026-02-15',
        ]);

        $response->assertRedirect(route('cpd.index'));
        $this->assertDatabaseHas('cpd_records', ['id' => $record->id, 'title' => 'New Title', 'category' => 'seminar']);
    }

    public function test_can_delete_cpd_record(): void
    {
        $record = CpdRecord::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'title' => 'Delete Me',
            'category' => 'course',
            'hours' => 5,
            'completed_date' => '2026-02-01',
            'verified' => false,
        ]);

        $response = $this->actingAs($this->user)->delete(route('cpd.destroy', $record));
        $response->assertRedirect(route('cpd.index'));
        $this->assertDatabaseMissing('cpd_records', ['id' => $record->id]);
    }

    public function test_can_verify_cpd_record(): void
    {
        $record = CpdRecord::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'title' => 'Verify Me',
            'category' => 'course',
            'hours' => 10,
            'completed_date' => '2026-02-01',
            'verified' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('cpd.verify', $record));
        $response->assertRedirect();
        $this->assertDatabaseHas('cpd_records', [
            'id' => $record->id,
            'verified' => true,
            'verified_by' => $this->user->id,
        ]);
    }

    public function test_category_label_accessor(): void
    {
        $record = new CpdRecord(['category' => 'self_study']);
        $this->assertEquals('Self Study', $record->category_label);

        $record2 = new CpdRecord(['category' => 'course']);
        $this->assertEquals('Course', $record2->category_label);
    }
}
