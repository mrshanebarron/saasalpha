<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\Enquiry;
use App\Models\Project;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::where('tenant_id', auth()->user()->tenant_id)
            ->with(['enquiry', 'preparedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('client_name', 'like', "%{$request->search}%")
                  ->orWhere('reference', 'like', "%{$request->search}%");
            });
        }

        $quotes = $query->latest()->paginate(20)->withQueryString();
        return view('quotes.index', compact('quotes'));
    }

    public function create(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $enquiries = Enquiry::where('tenant_id', $tenantId)->whereIn('status', ['new', 'reviewing', 'qualified'])->get();
        $enquiry = $request->filled('enquiry_id') ? Enquiry::find($request->enquiry_id) : null;
        return view('quotes.create', compact('enquiries', 'enquiry'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enquiry_id' => 'nullable|exists:enquiries,id',
            'client_name' => 'required|string|max:255',
            'scope_of_work' => 'nullable|string',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'valid_until' => 'nullable|date',
            'terms' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $reference = 'QUO-' . str_pad(Quote::where('tenant_id', $tenantId)->count() + 1, 4, '0', STR_PAD_LEFT);

        $quote = Quote::create([
            'tenant_id' => $tenantId,
            'enquiry_id' => $validated['enquiry_id'] ?? null,
            'reference' => $reference,
            'client_name' => $validated['client_name'],
            'scope_of_work' => $validated['scope_of_work'] ?? null,
            'tax_rate' => $validated['tax_rate'],
            'valid_until' => $validated['valid_until'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'prepared_by' => auth()->id(),
            'status' => 'draft',
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
        ]);

        foreach ($validated['items'] as $item) {
            $amount = round($item['quantity'] * $item['rate'], 2);
            $quote->lineItems()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'hours',
                'rate' => $item['rate'],
                'amount' => $amount,
            ]);
        }

        $quote->recalculate();
        AuditLog::log('created', $quote);

        return redirect()->route('quotes.show', $quote)->with('success', 'Quote created.');
    }

    public function show(Quote $quote)
    {
        $quote->load(['enquiry', 'preparedBy', 'lineItems', 'project']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $quote->load('lineItems');
        $tenantId = auth()->user()->tenant_id;
        $enquiries = Enquiry::where('tenant_id', $tenantId)->whereIn('status', ['new', 'reviewing', 'qualified'])->get();
        $lineItemsJson = $quote->lineItems->map(fn($li) => ['description' => $li->description, 'quantity' => (float) $li->quantity, 'unit' => $li->unit, 'rate' => (float) $li->rate])->values();
        return view('quotes.edit', compact('quote', 'enquiries', 'lineItemsJson'));
    }

    public function update(Request $request, Quote $quote)
    {
        $old = $quote->only(['status', 'client_name', 'total']);

        $validated = $request->validate([
            'enquiry_id' => 'nullable|exists:enquiries,id',
            'client_name' => 'required|string|max:255',
            'scope_of_work' => 'nullable|string',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'valid_until' => 'nullable|date',
            'terms' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $quote->update([
            'enquiry_id' => $validated['enquiry_id'] ?? $quote->enquiry_id,
            'client_name' => $validated['client_name'],
            'scope_of_work' => $validated['scope_of_work'] ?? $quote->scope_of_work,
            'tax_rate' => $validated['tax_rate'],
            'valid_until' => $validated['valid_until'] ?? $quote->valid_until,
            'terms' => $validated['terms'] ?? $quote->terms,
        ]);

        $quote->lineItems()->delete();
        foreach ($validated['items'] as $item) {
            $amount = round($item['quantity'] * $item['rate'], 2);
            $quote->lineItems()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'hours',
                'rate' => $item['rate'],
                'amount' => $amount,
            ]);
        }

        $quote->recalculate();
        AuditLog::log('updated', $quote, $old, $quote->only(['status', 'client_name', 'total']));

        return redirect()->route('quotes.show', $quote)->with('success', 'Quote updated.');
    }

    public function destroy(Quote $quote)
    {
        AuditLog::log('deleted', $quote);
        $quote->lineItems()->delete();
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted.');
    }

    public function markSent(Quote $quote)
    {
        $quote->update(['status' => 'sent', 'sent_at' => now()]);
        AuditLog::log('sent', $quote);
        return back()->with('success', 'Quote marked as sent.');
    }

    public function convertToProject(Quote $quote)
    {
        if ($quote->project) {
            return back()->with('error', 'This quote already has a project.');
        }

        $quote->update(['status' => 'accepted', 'accepted_at' => now()]);

        if ($quote->enquiry) {
            $quote->enquiry->update(['status' => 'converted']);
        }

        $tenantId = auth()->user()->tenant_id;
        $reference = 'PRJ-' . str_pad(Project::where('tenant_id', $tenantId)->count() + 1, 4, '0', STR_PAD_LEFT);

        $project = Project::create([
            'tenant_id' => $tenantId,
            'quote_id' => $quote->id,
            'reference' => $reference,
            'name' => $quote->scope_of_work ? substr($quote->scope_of_work, 0, 100) : $quote->client_name . ' Project',
            'client_name' => $quote->client_name,
            'project_type' => $quote->enquiry?->project_type ?? 'general',
            'status' => 'active',
            'budget' => $quote->total,
            'spent' => 0,
            'progress' => 0,
            'start_date' => now(),
            'target_date' => now()->addMonths(3),
            'project_manager_id' => auth()->id(),
        ]);

        AuditLog::log('created', $project, null, ['converted_from_quote' => $quote->reference]);

        return redirect()->route('projects.show', $project)->with('success', "Project {$reference} created from quote.");
    }

    public function pdf(Quote $quote)
    {
        $quote->load(['enquiry', 'lineItems']);
        $tenant = auth()->user()->tenant;
        return view('quotes.pdf', compact('quote', 'tenant'));
    }
}
