@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@section('title', 'Edit journal')
@section('pageTitle', 'Edit journal')
@section('pageDescription', $journal->name)

@section('content')
    <div class="mx-auto mb-4 max-w-2xl">
        <x-dash.link :href="platform_route('admin.journals.index')">← Journals</x-dash.link>
    </div>

    <form method="POST" action="{{ platform_route('admin.journals.update', $journal) }}" class="mx-auto max-w-2xl space-y-5 dash-card p-6">
        @csrf
        @method('PUT')
        <x-dash.input label="Name" name="name" :value="old('name', $journal->name)" required />
        <x-dash.input label="Subdomain" name="subdomain" :value="old('subdomain', $journal->subdomain)" pattern="[a-z0-9-]+" required />
        <x-dash.input label="ISSN (optional)" name="issn" :value="old('issn', $journal->issn)" />
        <label class="flex items-center gap-2 text-sm text-slate-800">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $journal->is_active)) class="dash-checkbox">
            Active (listed on the marketing site)
        </label>
        <x-dash.input label="Primary color (hex, optional)" name="primary_color" :value="old('primary_color', $journal->primary_color)" />
        <x-dash.textarea label="Description" name="description" rows="3">{{ old('description', $journal->description) }}</x-dash.textarea>
        <x-dash.textarea label="Submission guidelines" name="submission_guidelines" rows="5">{{ old('submission_guidelines', $journal->submission_guidelines) }}</x-dash.textarea>
        <x-dash.form-actions class="border-t border-slate-100 pt-4">
            <x-dash.button type="submit">Save changes</x-dash.button>
            <x-dash.button variant="secondary" :href="platform_route('admin.journals.index')">Back to list</x-dash.button>
        </x-dash.form-actions>
    </form>
@endsection
