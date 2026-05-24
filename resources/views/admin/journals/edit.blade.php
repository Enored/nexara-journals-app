@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@section('title', 'Edit journal')
@section('pageTitle', 'Edit journal')
@section('pageDescription', $journal->name)

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Journals', 'url' => platform_route('admin.journals.index')],
        ['label' => $journal->name, 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ platform_route('admin.journals.update', $journal) }}">
                        @csrf
                        @method('PUT')
                        <x-dash.input label="Name" name="name" :value="old('name', $journal->name)" required />
                        <x-dash.input label="Subdomain" name="subdomain" :value="old('subdomain', $journal->subdomain)" pattern="[a-z0-9-]+" required />
                        <x-dash.input label="ISSN (optional)" name="issn" :value="old('issn', $journal->issn)" />
                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    id="journal-is-active"
                                    class="form-check-input"
                                    @checked(old('is_active', $journal->is_active))
                                >
                                <label class="form-check-label" for="journal-is-active">Active (listed on the marketing site)</label>
                            </div>
                        </div>
                        <x-dash.input label="Primary color (hex, optional)" name="primary_color" :value="old('primary_color', $journal->primary_color)" />
                        <x-dash.textarea label="Description" name="description" rows="3">{{ old('description', $journal->description) }}</x-dash.textarea>
                        <x-dash.textarea label="Submission guidelines" name="submission_guidelines" rows="5">{{ old('submission_guidelines', $journal->submission_guidelines) }}</x-dash.textarea>
                        <x-dash.form-actions>
                            <x-dash.button type="submit">Save changes</x-dash.button>
                            <x-dash.button variant="secondary" :href="platform_route('admin.journals.index')">Back to list</x-dash.button>
                        </x-dash.form-actions>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
