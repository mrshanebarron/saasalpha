@extends('layouts.app')
@section('heading', 'Edit ' . $enquiry->reference)
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('enquiries.update', $enquiry) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Client Name *</label>
                <input type="text" name="client_name" value="{{ old('client_name', $enquiry->client_name) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                @error('client_name')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Company</label>
                <input type="text" name="client_company" value="{{ old('client_company', $enquiry->client_company) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Email</label>
                <input type="email" name="client_email" value="{{ old('client_email', $enquiry->client_email) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                <input type="text" name="client_phone" value="{{ old('client_phone', $enquiry->client_phone) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Source *</label>
                <select name="source" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['referral','website','cold_call','repeat','other'] as $s)
                        <option value="{{ $s }}" {{ old('source', $enquiry->source) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Status *</label>
                <select name="status" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['new','reviewing','qualified','converted','declined'] as $s)
                        <option value="{{ $s }}" {{ old('status', $enquiry->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Priority *</label>
                <select name="priority" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['low','normal','high','urgent'] as $p)
                        <option value="{{ $p }}" {{ old('priority', $enquiry->priority) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Project Type</label>
                <input type="text" name="project_type" value="{{ old('project_type', $enquiry->project_type) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Estimated Value</label>
                <input type="number" name="estimated_value" value="{{ old('estimated_value', $enquiry->estimated_value) }}" step="0.01" min="0" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', $enquiry->deadline?->format('Y-m-d')) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Assigned To</label>
                <select name="assigned_to" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    <option value="">Unassigned</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('assigned_to', $enquiry->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Description</label>
            <textarea name="description" rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('description', $enquiry->description) }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update Enquiry</button>
            <a href="{{ route('enquiries.show', $enquiry) }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
