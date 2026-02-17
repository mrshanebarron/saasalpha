@extends('layouts.app')
@section('heading', $quote->reference)
@section('content')
<div class="max-w-4xl">
    {{-- Actions --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('quotes.edit', $quote) }}" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700 transition">Edit</a>
        <a href="{{ route('quotes.pdf', $quote) }}" target="_blank" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700 transition">Download PDF</a>
        @if($quote->status === 'draft')
        <form method="POST" action="{{ route('quotes.send', $quote) }}">@csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Mark as Sent</button>
        </form>
        @endif
        @if(in_array($quote->status, ['sent', 'accepted']) && !$quote->project)
        <form method="POST" action="{{ route('quotes.convert', $quote) }}">@csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">Convert to Project</button>
        </form>
        @endif
        @if($quote->project)
        <a href="{{ route('projects.show', $quote->project) }}" class="px-4 py-2 bg-slate-800 text-brand-400 rounded-lg text-sm hover:bg-slate-700 transition">View Project</a>
        @endif
        <form method="POST" action="{{ route('quotes.destroy', $quote) }}" onsubmit="return confirm('Delete this quote and all line items?')" class="ml-auto">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 text-red-400 text-sm hover:text-red-300">Delete</button>
        </form>
    </div>

    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-{{ $quote->status_color }}-500/10 text-{{ $quote->status_color }}-400 mb-2">{{ ucfirst($quote->status) }}</span>
                <h2 class="text-xl font-bold text-white">{{ $quote->client_name }}</h2>
                <p class="text-sm text-slate-400">{{ $quote->client_company }}</p>
                @if($quote->enquiry)
                <p class="text-xs text-slate-500 mt-1">From enquiry <a href="{{ route('enquiries.show', $quote->enquiry) }}" class="text-brand-400 hover:text-brand-300">{{ $quote->enquiry->reference }}</a></p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-white">${{ number_format($quote->total) }}</div>
                <div class="text-xs text-slate-500">{{ $quote->currency ?? 'CAD' }} incl. {{ number_format($quote->tax_rate, 0) }}% HST</div>
            </div>
        </div>

        @if($quote->scope_of_work)
        <div class="border-t border-slate-800 pt-4 mb-6">
            <h4 class="text-xs font-medium text-slate-500 uppercase mb-2">Scope of Work</h4>
            <p class="text-sm text-slate-300 whitespace-pre-line">{{ $quote->scope_of_work }}</p>
        </div>
        @endif

        @if($quote->lineItems->count())
        <div class="border-t border-slate-800 pt-4">
            <h4 class="text-xs font-medium text-slate-500 uppercase mb-3">Line Items</h4>
            <table class="w-full">
                <thead><tr class="text-xs text-slate-500 border-b border-slate-800">
                    <th class="pb-2 text-left">Description</th><th class="pb-2 text-center">Qty</th><th class="pb-2 text-center">Unit</th><th class="pb-2 text-right">Rate</th><th class="pb-2 text-right">Amount</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-800/30">
                    @foreach($quote->lineItems as $li)
                    <tr>
                        <td class="py-2 text-sm text-slate-300">{{ $li->description }}</td>
                        <td class="py-2 text-sm text-slate-400 text-center">{{ number_format($li->quantity) }}</td>
                        <td class="py-2 text-sm text-slate-400 text-center capitalize">{{ $li->unit }}</td>
                        <td class="py-2 text-sm text-slate-400 text-right">${{ number_format($li->rate) }}</td>
                        <td class="py-2 text-sm font-medium text-slate-200 text-right">${{ number_format($li->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-slate-700">
                    <tr><td colspan="4" class="py-2 text-sm text-slate-400 text-right">Subtotal</td><td class="py-2 text-sm text-slate-200 text-right">${{ number_format($quote->subtotal) }}</td></tr>
                    <tr><td colspan="4" class="py-1 text-sm text-slate-400 text-right">HST ({{ number_format($quote->tax_rate, 0) }}%)</td><td class="py-1 text-sm text-slate-200 text-right">${{ number_format($quote->tax_amount) }}</td></tr>
                    <tr class="border-t border-slate-700"><td colspan="4" class="py-2 text-sm font-bold text-white text-right">Total</td><td class="py-2 text-lg font-bold text-white text-right">${{ number_format($quote->total) }}</td></tr>
                </tfoot>
            </table>
        </div>
        @endif

        @if($quote->terms)
        <div class="border-t border-slate-800 pt-4 mt-4"><h4 class="text-xs font-medium text-slate-500 uppercase mb-2">Terms</h4><p class="text-sm text-slate-400 whitespace-pre-line">{{ $quote->terms }}</p></div>
        @endif
    </div>
</div>
@endsection
