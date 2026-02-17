<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::where('tenant_id', auth()->user()->tenant_id)
            ->with('assignedTo')
            ->withCount('quotes');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('client_name', 'like', "%{$request->search}%")
                  ->orWhere('client_company', 'like', "%{$request->search}%")
                  ->orWhere('reference', 'like', "%{$request->search}%");
            });
        }

        $enquiries = $query->latest()->paginate(20)->withQueryString();
        return view('enquiries.index', compact('enquiries'));
    }

    public function create()
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('enquiries.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'source' => 'required|in:referral,website,cold_call,repeat,other',
            'project_type' => 'nullable|string|max:100',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_value' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'new';
        $validated['reference'] = 'ENQ-' . str_pad(Enquiry::where('tenant_id', auth()->user()->tenant_id)->count() + 1, 4, '0', STR_PAD_LEFT);

        $enquiry = Enquiry::create($validated);
        AuditLog::log('created', $enquiry);

        return redirect()->route('enquiries.show', $enquiry)->with('success', 'Enquiry created.');
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['assignedTo', 'quotes.preparedBy']);
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('enquiries.show', compact('enquiry', 'users'));
    }

    public function edit(Enquiry $enquiry)
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('enquiries.edit', compact('enquiry', 'users'));
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $old = $enquiry->only(['status', 'priority', 'assigned_to', 'estimated_value']);

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'source' => 'required|in:referral,website,cold_call,repeat,other',
            'project_type' => 'nullable|string|max:100',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:new,reviewing,qualified,converted,declined',
            'estimated_value' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $enquiry->update($validated);
        AuditLog::log('updated', $enquiry, $old, $enquiry->only(['status', 'priority', 'assigned_to', 'estimated_value']));

        return redirect()->route('enquiries.show', $enquiry)->with('success', 'Enquiry updated.');
    }

    public function destroy(Enquiry $enquiry)
    {
        AuditLog::log('deleted', $enquiry);
        $enquiry->delete();
        return redirect()->route('enquiries.index')->with('success', 'Enquiry deleted.');
    }
}
