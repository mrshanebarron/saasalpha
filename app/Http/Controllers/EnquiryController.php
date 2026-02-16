<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;

class EnquiryController extends Controller
{
    public function index()
    {
        $enquiries = Enquiry::where('tenant_id', auth()->user()->tenant_id)
            ->with('assignedTo')
            ->withCount('quotes')
            ->latest()
            ->get();
        return view('enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['assignedTo', 'quotes.preparedBy']);
        return view('enquiries.show', compact('enquiry'));
    }
}
