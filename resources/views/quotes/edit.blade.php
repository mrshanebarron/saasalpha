@extends('layouts.app')
@section('heading', 'Edit ' . $quote->reference)
@section('content')
<div class="max-w-3xl" x-data="quoteBuilder()">
    <form method="POST" action="{{ route('quotes.update', $quote) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Client Name *</label>
                <input type="text" name="client_name" value="{{ old('client_name', $quote->client_name) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Linked Enquiry</label>
                <select name="enquiry_id" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    <option value="">None</option>
                    @foreach($enquiries as $e)
                        <option value="{{ $e->id }}" {{ old('enquiry_id', $quote->enquiry_id) == $e->id ? 'selected' : '' }}>{{ $e->reference }} — {{ $e->client_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Scope of Work</label>
            <textarea name="scope_of_work" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('scope_of_work', $quote->scope_of_work) }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Tax Rate (%) *</label>
                <input type="number" name="tax_rate" x-model="taxRate" step="0.01" min="0" max="100" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Valid Until</label>
                <input type="date" name="valid_until" value="{{ old('valid_until', $quote->valid_until?->format('Y-m-d')) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Payment Terms</label>
                <input type="text" name="terms" value="{{ old('terms', $quote->terms) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        {{-- Line Items --}}
        <div class="border-t border-slate-800 pt-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-white">Line Items</h3>
                <button type="button" @click="addItem()" class="text-xs text-brand-400 hover:text-brand-300">+ Add Item</button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 items-start">
                        <div class="col-span-5">
                            <input type="text" :name="`items[${index}][description]`" x-model="item.description" placeholder="Description" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-brand-500">
                        </div>
                        <div class="col-span-2">
                            <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" placeholder="Qty" step="0.01" min="0.01" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-brand-500">
                        </div>
                        <div class="col-span-1">
                            <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" placeholder="Unit" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-brand-500">
                        </div>
                        <div class="col-span-2">
                            <input type="number" :name="`items[${index}][rate]`" x-model="item.rate" placeholder="Rate" step="0.01" min="0" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-brand-500">
                        </div>
                        <div class="col-span-1 text-right text-sm text-slate-300 py-2" x-text="'$' + (item.quantity * item.rate || 0).toFixed(0)"></div>
                        <div class="col-span-1 text-right">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-slate-500 hover:text-red-400 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Totals --}}
            <div class="mt-4 pt-4 border-t border-slate-800 space-y-1 text-right text-sm">
                <div class="text-slate-400">Subtotal: <span class="text-slate-200 font-medium" x-text="'$' + subtotal.toFixed(2)"></span></div>
                <div class="text-slate-400">Tax (<span x-text="taxRate"></span>%): <span class="text-slate-200" x-text="'$' + tax.toFixed(2)"></span></div>
                <div class="text-white font-bold text-base">Total: <span x-text="'$' + total.toFixed(2)"></span></div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update Quote</button>
            <a href="{{ route('quotes.show', $quote) }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
        </div>
    </form>
</div>

<script>
function quoteBuilder() {
    return {
        taxRate: {{ old('tax_rate', $quote->tax_rate) }},
        items: @json($quote->lineItems->map(fn($li) => ['description' => $li->description, 'quantity' => $li->quantity, 'unit' => $li->unit, 'rate' => $li->rate])->values()),
        addItem() { this.items.push({ description: '', quantity: 1, unit: 'hours', rate: 150 }); },
        removeItem(i) { this.items.splice(i, 1); },
        get subtotal() { return this.items.reduce((s, i) => s + (parseFloat(i.quantity) || 0) * (parseFloat(i.rate) || 0), 0); },
        get tax() { return this.subtotal * (parseFloat(this.taxRate) || 0) / 100; },
        get total() { return this.subtotal + this.tax; },
    }
}
</script>
@endsection
