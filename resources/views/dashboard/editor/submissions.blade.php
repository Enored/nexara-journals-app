@extends('layouts.dashboard', ['activeNav' => 'editor-submissions'])

@section('title', 'Submissions')
@section('pageTitle', 'Submissions')
@section('pageDescription', 'Manage manuscripts and peer review for your journals.')

@section('content')
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3">
        @include('partials.dashboard.stat-card', ['label' => 'All submissions', 'value' => $stats['total']])
        @include('partials.dashboard.stat-card', ['label' => 'In progress', 'value' => $stats['in_progress'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Under review', 'value' => $stats['under_review'], 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Accepted', 'value' => $stats['accepted'], 'accent' => 'violet'])
    </div>

    <x-dash.list-partial-zone>
        @include('dashboard.editor.partials.pipeline-list')
    </x-dash.list-partial-zone>
@endsection
