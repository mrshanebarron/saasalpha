<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $tenant->id]);
    }

    public function test_dashboard_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_audit_log_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('audit.index'));
        $response->assertStatus(200);
    }

    public function test_all_protected_get_routes_redirect_guests(): void
    {
        $protectedRoutes = [
            '/',
            '/enquiries',
            '/quotes',
            '/projects',
            '/time-tracking',
            '/compliance',
            '/cpd',
            '/subcontractors',
            '/audit',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login', "Route {$route} should redirect guests");
        }
    }
}
