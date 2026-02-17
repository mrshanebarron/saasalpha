<?php

namespace Tests\Feature;

use App\Models\DocumentTemplate;
use App\Models\GeneratedDocument;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTemplateTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('documents.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('documents.create'));
        $response->assertStatus(200);
    }

    public function test_can_store_template(): void
    {
        $response = $this->actingAs($this->user)->post(route('documents.store'), [
            'name' => 'Proposal Template',
            'type' => 'proposal',
            'content' => '<h2>Proposal for {{client_name}}</h2><p>Date: {{date}}</p>',
            'variables' => 'client_name, date',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_templates', [
            'name' => 'Proposal Template',
            'type' => 'proposal',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_store_parses_variables(): void
    {
        $this->actingAs($this->user)->post(route('documents.store'), [
            'name' => 'Test',
            'type' => 'letter',
            'content' => '{{foo}} {{bar}}',
            'variables' => 'foo, bar',
        ]);

        $template = DocumentTemplate::where('tenant_id', $this->tenant->id)->first();
        $this->assertEquals(['foo', 'bar'], $template->variables);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('documents.store'), []);
        $response->assertSessionHasErrors(['name', 'type', 'content']);
    }

    public function test_store_validates_type_enum(): void
    {
        $response = $this->actingAs($this->user)->post(route('documents.store'), [
            'name' => 'Test',
            'type' => 'invalid_type',
            'content' => 'test',
        ]);
        $response->assertSessionHasErrors('type');
    }

    public function test_show_page_loads(): void
    {
        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Template',
            'type' => 'proposal',
            'content' => 'Test content',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('documents.show', $template));
        $response->assertStatus(200);
    }

    public function test_edit_page_loads(): void
    {
        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Template',
            'type' => 'proposal',
            'content' => 'Test content',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('documents.edit', $template));
        $response->assertStatus(200);
    }

    public function test_can_update_template(): void
    {
        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Old Name',
            'type' => 'proposal',
            'content' => 'Old content',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('documents.update', $template), [
            'name' => 'New Name',
            'type' => 'report',
            'content' => 'New content',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_templates', [
            'id' => $template->id,
            'name' => 'New Name',
            'type' => 'report',
        ]);
    }

    public function test_can_delete_template(): void
    {
        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Delete Me',
            'type' => 'letter',
            'content' => 'content',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('documents.destroy', $template));
        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseMissing('document_templates', ['id' => $template->id]);
    }

    public function test_can_generate_document(): void
    {
        Storage::fake();

        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Proposal',
            'type' => 'proposal',
            'content' => '<p>Dear {{client_name}}, this is for {{project_name}}.</p>',
            'variables' => ['client_name', 'project_name'],
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('documents.generate', $template), [
            'title' => 'Generated Proposal',
            'variables' => ['client_name' => 'Acme Corp', 'project_name' => 'Bridge Build'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('generated_documents', [
            'template_id' => $template->id,
            'title' => 'Generated Proposal',
            'status' => 'generated',
        ]);

        $doc = GeneratedDocument::first();
        Storage::assertExists($doc->file_path);

        $content = Storage::get($doc->file_path);
        $this->assertStringContainsString('Acme Corp', $content);
        $this->assertStringContainsString('Bridge Build', $content);
    }

    public function test_generate_substitutes_builtin_variables(): void
    {
        Storage::fake();

        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Letter',
            'type' => 'letter',
            'content' => '<p>Date: {{date}}, Company: {{tenant_name}}, Author: {{user_name}}</p>',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('documents.generate', $template), [
            'title' => 'Test Letter',
        ]);

        $doc = GeneratedDocument::first();
        $content = Storage::get($doc->file_path);
        $this->assertStringContainsString(now()->format('F j, Y'), $content);
        $this->assertStringContainsString($this->tenant->name, $content);
        $this->assertStringContainsString($this->user->name, $content);
    }

    public function test_generate_increments_usage_count(): void
    {
        Storage::fake();

        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Counter Test',
            'type' => 'report',
            'content' => 'content',
            'usage_count' => 0,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('documents.generate', $template), [
            'title' => 'Doc 1',
        ]);

        $this->assertEquals(1, $template->fresh()->usage_count);
    }

    public function test_preview_loads(): void
    {
        Storage::fake();
        Storage::put('generated-docs/test.html', '<html><body>Test</body></html>');

        $template = DocumentTemplate::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Template',
            'type' => 'report',
            'content' => 'content',
            'created_by' => $this->user->id,
        ]);

        $doc = GeneratedDocument::create([
            'tenant_id' => $this->tenant->id,
            'template_id' => $template->id,
            'title' => 'Preview Test',
            'file_path' => 'generated-docs/test.html',
            'status' => 'generated',
            'generated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('documents.preview', $doc));
        $response->assertStatus(200);
    }

    public function test_creates_audit_log(): void
    {
        $response = $this->actingAs($this->user)->post(route('documents.store'), [
            'name' => 'Audit Test',
            'type' => 'certificate',
            'content' => 'content',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_templates', ['name' => 'Audit Test']);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'model_type' => 'DocumentTemplate',
        ]);
    }
}
