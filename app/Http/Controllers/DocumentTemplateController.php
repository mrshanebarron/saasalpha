<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\GeneratedDocument;
use App\Models\Project;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::where('tenant_id', auth()->user()->tenant_id)
            ->with('createdBy')
            ->latest()
            ->paginate(20);

        $generated = GeneratedDocument::where('tenant_id', auth()->user()->tenant_id)
            ->with(['template', 'project', 'generatedBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('documents.index', compact('templates', 'generated'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:proposal,report,certificate,letter,invoice',
            'content' => 'required|string',
            'variables' => 'nullable|string',
        ]);

        $variables = ($validated['variables'] ?? null)
            ? array_map('trim', explode(',', $validated['variables']))
            : [];

        $template = DocumentTemplate::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'content' => $validated['content'],
            'variables' => $variables,
            'created_by' => auth()->id(),
        ]);

        AuditLog::log('created', $template);

        return redirect()->route('documents.show', $template)->with('success', 'Template created.');
    }

    public function show(DocumentTemplate $documentTemplate)
    {
        $documentTemplate->load('generatedDocuments.project', 'generatedDocuments.generatedBy');
        $projects = Project::where('tenant_id', auth()->user()->tenant_id)->where('status', 'active')->get();
        return view('documents.show', compact('documentTemplate', 'projects'));
    }

    public function edit(DocumentTemplate $documentTemplate)
    {
        return view('documents.edit', compact('documentTemplate'));
    }

    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:proposal,report,certificate,letter,invoice',
            'content' => 'required|string',
            'variables' => 'nullable|string',
        ]);

        $variables = ($validated['variables'] ?? null)
            ? array_map('trim', explode(',', $validated['variables']))
            : [];

        $documentTemplate->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'content' => $validated['content'],
            'variables' => $variables,
        ]);

        AuditLog::log('updated', $documentTemplate);

        return redirect()->route('documents.show', $documentTemplate)->with('success', 'Template updated.');
    }

    public function destroy(DocumentTemplate $documentTemplate)
    {
        AuditLog::log('deleted', $documentTemplate);
        $documentTemplate->delete();
        return redirect()->route('documents.index')->with('success', 'Template deleted.');
    }

    public function generate(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'required|string|max:255',
            'variables' => 'nullable|array',
        ]);

        $content = $documentTemplate->content;
        $project = ($validated['project_id'] ?? null) ? Project::find($validated['project_id']) : null;

        // Built-in variables from project context
        $builtins = [
            'date' => now()->format('F j, Y'),
            'tenant_name' => auth()->user()->tenant->name,
            'user_name' => auth()->user()->name,
            'user_title' => auth()->user()->job_title ?? '',
        ];

        if ($project) {
            $builtins['project_name'] = $project->name;
            $builtins['project_reference'] = $project->reference;
            $builtins['client_name'] = $project->client_name;
            $builtins['client_company'] = $project->client_company ?? '';
            $builtins['project_budget'] = number_format($project->budget ?? 0, 2);
            $builtins['project_status'] = $project->status;

            if ($project->quote) {
                $builtins['quote_reference'] = $project->quote->reference;
                $builtins['quote_total'] = number_format($project->quote->total, 2);
                $builtins['scope_of_work'] = $project->quote->scope_of_work ?? '';
            }
        }

        // Merge built-in + user-provided variables
        $vars = array_merge($builtins, $validated['variables'] ?? []);

        // Substitute {{variable_name}} placeholders
        foreach ($vars as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        $doc = GeneratedDocument::create([
            'tenant_id' => auth()->user()->tenant_id,
            'template_id' => $documentTemplate->id,
            'project_id' => $validated['project_id'] ?? null,
            'title' => $validated['title'],
            'file_path' => null,
            'status' => 'draft',
            'generated_by' => auth()->id(),
        ]);

        // Store rendered content in a simple text file
        $path = 'generated-docs/' . $doc->id . '.html';
        $htmlContent = $this->wrapInHtml($doc->title, $content, auth()->user()->tenant);
        \Illuminate\Support\Facades\Storage::put($path, $htmlContent);
        $doc->update(['file_path' => $path, 'status' => 'generated']);

        $documentTemplate->increment('usage_count');
        AuditLog::log('generated', $doc);

        return redirect()->route('documents.preview', $doc)->with('success', 'Document generated.');
    }

    public function preview(GeneratedDocument $generatedDocument)
    {
        $content = $generatedDocument->file_path
            ? \Illuminate\Support\Facades\Storage::get($generatedDocument->file_path)
            : '<p>No content generated.</p>';

        return view('documents.preview', compact('generatedDocument', 'content'));
    }

    private function wrapInHtml(string $title, string $content, $tenant): string
    {
        $tenantName = e($tenant->name);
        $date = now()->format('F j, Y');
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <style>
        body { font-family: 'Georgia', serif; max-width: 800px; margin: 40px auto; padding: 40px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 24px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        h2 { font-size: 18px; color: #374151; margin-top: 24px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; }
        .company { font-size: 20px; font-weight: bold; color: #1e40af; }
        .date { color: #6b7280; font-size: 14px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; }
        @media print { body { margin: 0; padding: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">{$tenantName}</div>
        <div class="date">{$date}</div>
    </div>
    <h1>{$title}</h1>
    {$content}
    <div class="footer">Generated by {$tenantName} &middot; SaaS Alpha Platform &middot; {$date}</div>
</body>
</html>
HTML;
    }
}
