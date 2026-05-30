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
                        <x-dash.input label="Citation abbreviation (optional)" name="abbreviation" :value="old('abbreviation', $journal->abbreviation)" placeholder="J. Cogn. Neurosci." />
                        <x-dash.input label="Subdomain" name="subdomain" :value="old('subdomain', $journal->subdomain)" pattern="[a-z0-9-]+" required />
                        <x-dash.input label="Electronic ISSN (optional)" name="e_issn" :value="old('e_issn', $journal->e_issn)" placeholder="2845-1739" />
                        <x-dash.input label="Print ISSN (optional)" name="p_issn" :value="old('p_issn', $journal->p_issn)" placeholder="2845-1720" />
                        <x-dash.input label="DOI prefix (optional)" name="doi_prefix" :value="old('doi_prefix', $journal->doi_prefix)" placeholder="10.31472" />
                        <x-dash.select label="Review model" name="review_model">
                            @foreach (\App\Enums\ReviewModel::cases() as $model)
                                <option value="{{ $model->value }}" @selected(old('review_model', $journal->review_model?->value) === $model->value)>{{ $model->label() }}</option>
                            @endforeach
                        </x-dash.select>
                        <x-dash.input label="Publication frequency (optional)" name="frequency" :value="old('frequency', $journal->frequency)" placeholder="Continuous, Quarterly…" />
                        <x-dash.input label="License type (optional)" name="license_type" :value="old('license_type', $journal->license_type)" placeholder="CC BY 4.0" />
                        <x-dash.input label="Contact email (optional)" name="contact_email" type="email" :value="old('contact_email', $journal->contact_email)" />
                        <x-dash.input label="Cover image URL (optional)" name="cover_image_url" :value="old('cover_image_url', $journal->cover_image_url)" placeholder="https://…" />
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
                        <x-dash.textarea label="Excerpt (optional)" name="excerpt" rows="2">{{ old('excerpt', $journal->excerpt) }}</x-dash.textarea>
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
