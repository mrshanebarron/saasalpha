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

class AiSuggestionTest extends TestCase
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

    public function test_client_suggestions_returns_json(): void
    {
        Enquiry::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'ENQ-0001',
            'client_name' => 'Acme Corporation',
            'source' => 'referral',
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.clients', ['q' => 'Acme']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['Acme Corporation']);
    }

    public function test_client_suggestions_returns_empty_for_short_query(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.clients', ['q' => 'A']));
        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_client_suggestions_deduplicates(): void
    {
        Enquiry::create(['tenant_id' => $this->tenant->id, 'reference' => 'ENQ-0001', 'client_name' => 'Smith Corp', 'source' => 'referral', 'priority' => 'normal', 'status' => 'new']);
        Enquiry::create(['tenant_id' => $this->tenant->id, 'reference' => 'ENQ-0002', 'client_name' => 'Smith Corp', 'source' => 'website', 'priority' => 'normal', 'status' => 'new']);

        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.clients', ['q' => 'Smith']));
        $data = $response->json();
        $this->assertCount(1, $data);
    }

    public function test_client_suggestions_searches_quotes(): void
    {
        $quote = Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QT-0001',
            'client_name' => 'QuoteClient LLC',
            'status' => 'draft',
            'subtotal' => 1000,
            'tax_amount' => 130,
            'total' => 1130,
            'tax_rate' => 13,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.clients', ['q' => 'QuoteClient']));
        $response->assertJsonFragment(['QuoteClient LLC']);
    }

    public function test_client_suggestions_searches_projects(): void
    {
        Project::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'PRJ-0001',
            'name' => 'Big Bridge',
            'client_name' => 'ProjectClient Inc',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.clients', ['q' => 'ProjectClient']));
        $response->assertJsonFragment(['ProjectClient Inc']);
    }

    public function test_scope_suggestions_returns_json(): void
    {
        Quote::create([
            'tenant_id' => $this->tenant->id,
            'reference' => 'QT-0001',
            'client_name' => 'Test',
            'scope_of_work' => 'Full structural assessment and design review',
            'status' => 'sent',
            'subtotal' => 5000,
            'tax_amount' => 650,
            'total' => 5650,
            'tax_rate' => 13,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.scope'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['scopes', 'defaults']);
    }

    public function test_scope_suggestions_includes_smart_defaults(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.scope'));
        $data = $response->json();

        $this->assertArrayHasKey('defaults', $data);
        $this->assertArrayHasKey('time_category', $data['defaults']);
        $this->assertArrayHasKey('hourly_rate', $data['defaults']);
        $this->assertArrayHasKey('tax_rate', $data['defaults']);
        $this->assertArrayHasKey('enquiry_source', $data['defaults']);
    }

    public function test_client_suggestions_limited_to_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        Enquiry::create(['tenant_id' => $otherTenant->id, 'reference' => 'ENQ-9001', 'client_name' => 'OtherTenant Corp', 'source' => 'referral', 'priority' => 'normal', 'status' => 'new']);
        Enquiry::create(['tenant_id' => $this->tenant->id, 'reference' => 'ENQ-9002', 'client_name' => 'MyTenant Corp', 'source' => 'referral', 'priority' => 'normal', 'status' => 'new']);

        $response = $this->actingAs($this->user)->getJson(route('api.suggestions.clients', ['q' => 'Corp']));
        $data = $response->json();

        $this->assertContains('MyTenant Corp', $data);
        $this->assertNotContains('OtherTenant Corp', $data);
    }
}
