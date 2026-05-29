@extends('layouts.dashboard', ['activeNav' => 'author-submissions'])

@section('title', 'My submissions')
@section('pageTitle', 'My submissions')
@section('pageDescription', 'Submit new manuscripts and track their progress.')

@section('content')
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3">
        @include('partials.dashboard.stat-card', ['label' => 'Total', 'value' => $stats['total']])
        @include('partials.dashboard.stat-card', ['label' => 'Active', 'value' => $stats['active'], 'hint' => 'In editorial workflow', 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Revision due', 'value' => $stats['revision'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Published', 'value' => $stats['published'], 'accent' => 'violet'])
    </div>

    <x-dash.list-partial-zone>
        @include('dashboard.author.partials.submissions-list')
    </x-dash.list-partial-zone>
@endsection

@push('modals')
    @if ($submitJournals->isNotEmpty())
        <div
            class="modal fade"
            id="manuscript-create-modal"
            tabindex="-1"
            aria-labelledby="manuscript-create-modal-title"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form
                        method="POST"
                        action="{{ platform_route('author.submissions.store') }}"
                        id="manuscript-create-form"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="manuscript-create-modal-title">Create manuscript</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <x-dash.select label="Journal" name="journal_id" required>
                                <option value="" disabled @selected(! old('journal_id') && ! request('journal'))>Select a journal…</option>
                                @foreach ($submitJournals as $journal)
                                    <option value="{{ $journal->id }}" @selected(old('journal_id') === $journal->id || (! old('journal_id') && request('journal') === $journal->id))>
                                        {{ $journal->name }}
                                    </option>
                                @endforeach
                            </x-dash.select>
                            <x-dash.input
                                label="Title"
                                name="title"
                                :value="old('title')"
                                required
                            />
                            <x-dash.textarea label="Abstract" name="abstract" rows="5" required>{{ old('abstract') }}</x-dash.textarea>
                            <x-dash.input
                                label="Keywords"
                                name="keywords"
                                :value="old('keywords')"
                                required
                            />
                            <p class="form-text text-muted mt-n3 mb-3">Separate keywords with commas.</p>
                            <x-dash.select label="Article type" name="article_type" required>
                                <option value="" disabled @selected(! old('article_type'))>Select type…</option>
                                @foreach ($articleTypes as $type)
                                    <option value="{{ $type }}" @selected(old('article_type') === $type)>{{ $type }}</option>
                                @endforeach
                            </x-dash.select>
                            <div class="mb-0">
                                <label for="manuscript-file" class="form-label">Manuscript file</label>
                                <input
                                    type="file"
                                    name="manuscript"
                                    id="manuscript-file"
                                    class="form-control @error('manuscript') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    required
                                >
                                @error('manuscript')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <p class="form-text text-muted mb-0">PDF or Word (.doc, .docx), max 20 MB. Stored securely on the server for now.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit manuscript</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shouldOpen = {{ ($errors->hasAny(['journal_id', 'title', 'abstract', 'keywords', 'article_type', 'manuscript']) || request('create')) ? 'true' : 'false' }};
            if (shouldOpen) {
                const modalEl = document.getElementById('manuscript-create-modal');
                if (modalEl && window.bootstrap?.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            }
        });
    </script>
@endpush
