@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@section('title', 'New journal')
@section('pageTitle', 'New journal')
@section('pageDescription', 'Subdomain URL: https://{subdomain}.' . config('journal.base_domain'))

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Journals', 'url' => platform_route('admin.journals.index')],
        ['label' => 'New journal', 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ platform_route('admin.journals.store') }}">
                        @csrf
                        <x-dash.input label="Name" name="name" :value="old('name')" required />
                        <x-dash.input label="Subdomain (lowercase, numbers, hyphens)" name="subdomain" :value="old('subdomain')" pattern="[a-z0-9-]+" required />
                        <x-dash.input label="ISSN (optional)" name="issn" :value="old('issn')" />
                        <x-dash.input label="Primary color (hex, optional)" name="primary_color" :value="old('primary_color')" placeholder="#0f766e" />
                        <x-dash.textarea label="Description" name="description" rows="3">{{ old('description') }}</x-dash.textarea>
                        <x-dash.textarea label="Submission guidelines" name="submission_guidelines" rows="5">{{ old('submission_guidelines') }}</x-dash.textarea>
                        <x-dash.form-actions>
                            <x-dash.button type="submit">Create journal</x-dash.button>
                            <x-dash.button variant="secondary" :href="platform_route('admin.journals.index')">Cancel</x-dash.button>
                        </x-dash.form-actions>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
