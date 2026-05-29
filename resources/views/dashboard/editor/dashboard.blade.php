@extends('layouts.dashboard', ['activeNav' => $activeNav])

@section('title', $title)
@section('pageTitle', $pageTitle)
@section('pageDescription', $pageDescription)

@section('content')
    <div class="row row-cols-md-3 row-cols-1 g-3">
        @include('partials.dashboard.stat-card', ['label' => 'In progress', 'value' => $stats['in_progress'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Accepted', 'value' => $stats['accepted'], 'accent' => 'violet'])
        @include('partials.dashboard.stat-card', ['label' => 'Journals', 'value' => $stats['journals'], 'accent' => 'teal'])
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Quick links</h5>
            <div class="d-flex flex-wrap gap-2">
                <x-dash.button :href="platform_route('editor.submissions')">Submissions</x-dash.button>
                <x-dash.button variant="secondary" :href="platform_route('editor.journals.index')">Issues &amp; volumes</x-dash.button>
            </div>
        </div>
    </div>
@endsection
