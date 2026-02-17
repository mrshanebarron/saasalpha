<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
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

    public function test_mark_all_read(): void
    {
        Notification::create(['tenant_id' => $this->tenant->id, 'user_id' => $this->user->id, 'type' => 'info', 'title' => 'Note 1', 'message' => 'Test', 'is_read' => false]);
        Notification::create(['tenant_id' => $this->tenant->id, 'user_id' => $this->user->id, 'type' => 'info', 'title' => 'Note 2', 'message' => 'Test', 'is_read' => false]);

        $response = $this->actingAs($this->user)->post(route('notifications.mark-all-read'));
        $response->assertRedirect();

        $this->assertEquals(0, Notification::where('user_id', $this->user->id)->where('is_read', false)->count());
    }

    public function test_mark_single_read(): void
    {
        $notification = Notification::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'type' => 'info',
            'title' => 'Test',
            'message' => 'Click me',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('notifications.read', $notification));
        $response->assertRedirect();

        $this->assertTrue($notification->fresh()->is_read);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_read_redirects_to_action_url(): void
    {
        $notification = Notification::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'type' => 'info',
            'title' => 'New Project',
            'message' => 'Check it out',
            'action_url' => '/projects',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('notifications.read', $notification));
        $response->assertRedirect('/projects');
    }

    public function test_mark_as_read_model_method(): void
    {
        $notification = Notification::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'type' => 'info',
            'title' => 'Model Test',
            'message' => 'Test',
            'is_read' => false,
        ]);

        $notification->markAsRead();

        $this->assertTrue($notification->fresh()->is_read);
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
