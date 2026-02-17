@extends('layouts.app')
@section('heading', 'New Enquiry')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('enquiries.store') }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-5">
            <div x-data="clientAutocomplete()" class="relative">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Client Name * <span class="text-brand-500 text-[10px] font-normal ml-1">AI-assisted</span></label>
                <input type="text" name="client_name" x-model="query" @input.debounce.300ms="search()" @focus="open = suggestions.length > 0" @click.away="open = false" value="{{ old('client_name') }}" required autocomplete="off" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                <div x-show="open && suggestions.length > 0" x-cloak class="absolute z-20 mt-1 w-full bg-slate-800 border border-slate-700 rounded-lg shadow-xl overflow-hidden">
                    <template x-for="s in suggestions" :key="s">
                        <button type="button" @click="select(s)" class="w-full text-left px-3 py-2 text-sm text-slate-300 hover:bg-brand-600/20 hover:text-white" x-text="s"></button>
                    </template>
                </div>
                @error('client_name')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Company</label>
                <input type="text" name="client_company" value="{{ old('client_company') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Email</label>
                <input type="email" name="client_email" value="{{ old('client_email') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                <input type="text" name="client_phone" value="{{ old('client_phone') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Source *</label>
                <select name="source" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['referral','website','cold_call','repeat','other'] as $s)
                        <option value="{{ $s }}" {{ old('source') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Priority *</label>
                <select name="priority" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['low','normal','high','urgent'] as $p)
                        <option value="{{ $p }}" {{ old('priority', 'normal') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Project Type</label>
                <input type="text" name="project_type" value="{{ old('project_type') }}" placeholder="e.g. Structural, MEP" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estimated Value</label>
                <input type="number" name="estimated_value" value="{{ old('estimated_value') }}" step="0.01" min="0" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Assigned To</label>
                <select name="assigned_to" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    <option value="">Unassigned</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Description</label>
            <textarea name="description" rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Create Enquiry</button>
            <a href="{{ route('enquiries.index') }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
        </div>
    </form>
</div>
<script>
function clientAutocomplete() {
    return {
        query: '{{ old("client_name") }}',
        suggestions: [],
        open: false,
        async search() {
            if (this.query.length < 2) { this.suggestions = []; this.open = false; return; }
            const res = await fetch(`/api/suggestions/clients?q=${encodeURIComponent(this.query)}`);
            this.suggestions = await res.json();
            this.open = this.suggestions.length > 0;
        },
        select(name) { this.query = name; this.open = false; }
    }
}
</script>
@endsection
