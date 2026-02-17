<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('quotes.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('quotes.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_quote_with_line_items(): void
    {
        $response = $this->actingAs($this->user)->post(route('quotes.store'), [
            'client_name' => 'Test Client',
            'scope_of_work' => 'Structural assessment of bridge',
            'tax_rate' => 13,
            'terms' => 'Net 30',
            'items' => [
                ['description' => 'Site inspection', 'quantity' => 8, 'unit' => 'hours', 'rate' => 150],
                ['description' => 'Report writing', 'quantity' => 4, 'unit' => 'hours', 'rate' => 150],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('quotes', [
            'client_name' => 'Test Client',
            'status' => 'draft',
            'tenant_id' => $this->tenant->id,
            'terms' => 'Net 30',
        ]);

        $quote = Quote::where('client_name', 'Test Client')->first();
        $this->assertEquals(2, $quote->lineItems()->count());
        $this->assertEquals('QUO-0001', $quote->reference);
    }

    public function test_store_calculates_totals(): void
    {
        $this->actingAs($this->user)->post(route('quotes.store'), [
            'client_name' => 'Calc Client',
            'scope_of_work' => 'Calculate totals test',
            'tax_rate' => 13,
            'items' => [
                ['description' => 'Work', 'quantity' => 10, 'unit' => 'hours', 'rate' => 100],
            ],
        ]);

        $quote = Quote::where('client_name', 'Calc Client')->first();
        $this->assertEquals(1000, $quote->subtotal);
        $this->assertEquals(130, $quote->tax_amount);
        $this->assertEquals(1130, $quote->total);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('quotes.store'), []);
        $response->assertSessionHasErrors(['client_name', 'tax_rate', 'items']);
    }

    public function test_store_validates_line_items(): void
    {
        $response = $this->actingAs($this->user)->post(route('quotes.store'), [
            'client_name' => 'Test',
            'tax_rate' => 13,
            'items' => [
                ['description' => '', 'quantity' => 0, 'rate' => -1],
            ],
        ]);
        $response->assertSessionHasErrors([
            'items.0.description',
            'items.0.quantity',
            'items.0.rate',
        ]);
    }

    public function test_show_page_loads(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Show Test',
            'scope_of_work' => 'Test scope',
            'status' => 'draft',
            'tax_rate' => 13,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'prepared_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('quotes.show', $quote));
        $response->assertStatus(200);
    }

    public function test_edit_page_loads(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Edit Test',
            'scope_of_work' => 'Test scope',
            'status' => 'draft',
            'tax_rate' => 13,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'prepared_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('quotes.edit', $quote));
        $response->assertStatus(200);
    }

    public function test_can_update_quote(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Old Client',
            'scope_of_work' => 'Test scope',
            'status' => 'draft',
            'tax_rate' => 13,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'prepared_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('quotes.update', $quote), [
            'client_name' => 'New Client',
            'tax_rate' => 15,
            'terms' => 'Updated terms',
            'items' => [
                ['description' => 'New work', 'quantity' => 5, 'unit' => 'hours', 'rate' => 200],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'client_name' => 'New Client',
            'terms' => 'Updated terms',
        ]);
    }

    public function test_update_replaces_line_items(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Test',
            'scope_of_work' => 'Test scope',
            'status' => 'draft',
            'tax_rate' => 13,
            'subtotal' => 1000,
            'tax_amount' => 130,
            'total' => 1130,
            'prepared_by' => $this->user->id,
        ]);
        $quote->lineItems()->create(['description' => 'Old item', 'quantity' => 1, 'unit' => 'hours', 'rate' => 100, 'amount' => 100]);

        $this->actingAs($this->user)->put(route('quotes.update', $quote), [
            'client_name' => 'Test',
            'tax_rate' => 13,
            'items' => [
                ['description' => 'New item A', 'quantity' => 2, 'unit' => 'hours', 'rate' => 200],
                ['description' => 'New item B', 'quantity' => 3, 'unit' => 'hours', 'rate' => 150],
            ],
        ]);

        $this->assertEquals(2, $quote->fresh()->lineItems()->count());
        $this->assertDatabaseMissing('quote_line_items', ['description' => 'Old item']);
    }

    public function test_can_delete_quote(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Delete Me',
            'scope_of_work' => 'Test scope',
            'status' => 'draft',
            'tax_rate' => 13,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'prepared_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('quotes.destroy', $quote));
        $response->assertRedirect(route('quotes.index'));
        $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
    }

    public function test_mark_as_sent(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Send Test',
            'scope_of_work' => 'Test scope',
            'status' => 'draft',
            'tax_rate' => 13,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'prepared_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('quotes.send', $quote));
        $response->assertRedirect();
        $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'status' => 'sent']);
        $this->assertNotNull($quote->fresh()->sent_at);
    }

    public function test_convert_to_project(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Convert Client',
            'scope_of_work' => 'Build a bridge',
            'status' => 'sent',
            'tax_rate' => 13,
            'subtotal' => 1000,
            'tax_amount' => 130,
            'total' => 1130,
            'prepared_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('quotes.convert', $quote));

        // Quote should be accepted
        $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'status' => 'accepted']);

        // Project should be created
        $this->assertDatabaseHas('projects', [
            'client_name' => 'Convert Client',
            'quote_id' => $quote->id,
            'status' => 'active',
            'budget' => 1130,
        ]);

        $project = Project::where('quote_id', $quote->id)->first();
        $response->assertRedirect(route('projects.show', $project));
    }

    public function test_convert_fails_if_already_has_project(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Already Converted',
            'scope_of_work' => 'Test scope',
            'status' => 'accepted',
            'tax_rate' => 13,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'prepared_by' => $this->user->id,
        ]);
        Project::create([
            'tenant_id' => $this->tenant->id,
            'quote_id' => $quote->id,
            'reference' => 'PRJ-0001',
            'name' => 'Existing Project',
            'client_name' => 'Already Converted',
            'status' => 'active',
            'spent' => 0,
            'progress' => 0,
            'project_manager_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('quotes.convert', $quote));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_convert_updates_enquiry_status(): void
    {
        $enquiry = Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Enquiry Client',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'qualified',
        ]);

        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'enquiry_id' => $enquiry->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Enquiry Client',
            'scope_of_work' => 'Test scope',
            'status' => 'sent',
            'tax_rate' => 13,
            'subtotal' => 1000,
            'tax_amount' => 130,
            'total' => 1130,
            'prepared_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('quotes.convert', $quote));
        $this->assertDatabaseHas('enquiries', ['id' => $enquiry->id, 'status' => 'converted']);
    }

    public function test_pdf_page_loads(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'PDF Test',
            'scope_of_work' => 'Test scope',
            'status' => 'sent',
            'tax_rate' => 13,
            'subtotal' => 1000,
            'tax_amount' => 130,
            'total' => 1130,
            'prepared_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('quotes.pdf', $quote));
        $response->assertStatus(200);
        $response->assertSee('QUO-0001');
    }

    public function test_recalculate_computes_correct_totals(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QUO-0001',
            'client_name' => 'Recalc Test',
            'scope_of_work' => 'Test scope',
            'status' => 'draft',
            'tax_rate' => 13,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'prepared_by' => $this->user->id,
        ]);

        $quote->lineItems()->create(['description' => 'A', 'quantity' => 10, 'unit' => 'hours', 'rate' => 100, 'amount' => 1000]);
        $quote->lineItems()->create(['description' => 'B', 'quantity' => 5, 'unit' => 'hours', 'rate' => 200, 'amount' => 1000]);

        $quote->recalculate();

        $this->assertEquals(2000, $quote->subtotal);
        $this->assertEquals(260, $quote->tax_amount);
        $this->assertEquals(2260, $quote->total);
    }
}
