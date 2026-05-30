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
                        <x-dash.input label="Citation abbreviation (optional)" name="abbreviation" :value="old('abbreviation')" placeholder="J. Cogn. Neurosci." />
                        <x-dash.input label="Subdomain (lowercase, numbers, hyphens)" name="subdomain" :value="old('subdomain')" pattern="[a-z0-9-]+" required />
                        <x-dash.input label="Electronic ISSN (optional)" name="e_issn" :value="old('e_issn')" placeholder="2845-1739" />
                        <x-dash.input label="Print ISSN (optional)" name="p_issn" :value="old('p_issn')" placeholder="2845-1720" />
                        <x-dash.input label="DOI prefix (optional)" name="doi_prefix" :value="old('doi_prefix')" placeholder="10.31472" />
                        <x-dash.select label="Review model" name="review_model">
                            @foreach (\App\Enums\ReviewModel::cases() as $model)
                                <option value="{{ $model->value }}" @selected(old('review_model', \App\Enums\ReviewModel::SingleBlind->value) === $model->value)>{{ $model->label() }}</option>
                            @endforeach
                        </x-dash.select>
                        <x-dash.input label="Publication frequency (optional)" name="frequency" :value="old('frequency')" placeholder="Continuous, Quarterly…" />
                        <x-dash.input label="License type (optional)" name="license_type" :value="old('license_type')" placeholder="CC BY 4.0" />
                        <x-dash.input label="Contact email (optional)" name="contact_email" type="email" :value="old('contact_email')" />
                        <x-dash.input label="Cover image URL (optional)" name="cover_image_url" :value="old('cover_image_url')" placeholder="https://…" />
                        <x-dash.input label="Primary color (hex, optional)" name="primary_color" :value="old('primary_color')" placeholder="#0f766e" />
                        <x-dash.textarea label="Excerpt (optional)" name="excerpt" rows="2">{{ old('excerpt') }}</x-dash.textarea>
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
