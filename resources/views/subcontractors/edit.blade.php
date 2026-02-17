@extends('layouts.app')
@section('heading', 'Edit — ' . $subcontractor->company_name)
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('subcontractors.update', $subcontractor) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Company Name *</label>
            <input type="text" name="company_name" value="{{ old('company_name', $subcontractor->company_name) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Contact Name</label>
                <input type="text" name="contact_name" value="{{ old('contact_name', $subcontractor->contact_name) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Specialty</label>
                <input type="text" name="specialty" value="{{ old('specialty', $subcontractor->specialty) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $subcontractor->email) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $subcontractor->phone) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Default Rate ($/hr)</label>
                <input type="number" name="default_rate" value="{{ old('default_rate', $subcontractor->default_rate) }}" step="0.01" min="0" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Status *</label>
                <select name="status" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    <option value="active" {{ old('status', $subcontractor->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $subcontractor->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Notes</label>
            <textarea name="notes" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('notes', $subcontractor->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update</button>
                <a href="{{ route('subcontractors.show', $subcontractor) }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
            </div>
            <form method="POST" action="{{ route('subcontractors.destroy', $subcontractor) }}" onsubmit="return confirm('Delete this subcontractor?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-400 text-sm hover:text-red-300">Delete</button>
            </form>
        </div>
    </form>
</div>
@endsection
