<?php

namespace App\Http\Controllers;

use App\Models\Quote;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::where('tenant_id', auth()->user()->tenant_id)
            ->with(['enquiry', 'preparedBy'])
            ->latest()
            ->get();
        return view('quotes.index', compact('quotes'));
    }

    public function show(Quote $quote)
    {
        $quote->load(['enquiry', 'preparedBy', 'lineItems', 'project']);
        return view('quotes.show', compact('quote'));
    }
}
