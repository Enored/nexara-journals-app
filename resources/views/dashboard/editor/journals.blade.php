@extends('layouts.dashboard', ['activeNav' => 'editor-editions'])

@section('title', 'Issues & volumes')
@section('pageTitle', 'Issues & volumes')
@section('pageDescription', 'Manage volumes, issues, and publishing for your journals.')

@section('content')
    <div class="row g-3">
        @foreach ($journals as $journal)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1">{{ $journal->name }}</h5>
                        @if ($journal->issn)
                            <p class="text-muted small mb-3">ISSN {{ $journal->issn }}</p>
                        @else
                            <p class="text-muted small mb-3">Subdomain: {{ $journal->subdomain }}</p>
                        @endif
                        <p class="text-muted small flex-grow-1">
                            Create volumes and issues, assign accepted manuscripts, and publish or unpublish on the journal site.
                        </p>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <x-dash.button :href="platform_route('journal.editions.index', $journal)">
                                Manage issues
                            </x-dash.button>
                            <x-dash.button variant="secondary" :href="platform_route('editor.submissions', ['journal' => $journal->subdomain])">
                                Submissions
                            </x-dash.button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
