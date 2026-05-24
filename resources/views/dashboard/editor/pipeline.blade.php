@extends('layouts.dashboard', ['activeNav' => 'editor-pipeline'])

@section('title', 'Editorial pipeline')
@section('pageTitle', 'Editorial pipeline')
@section('pageDescription', 'Manage submissions and peer review for your journals.')

@section('content')
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3">
        @include('partials.dashboard.stat-card', ['label' => 'All submissions', 'value' => $stats['total']])
        @include('partials.dashboard.stat-card', ['label' => 'In pipeline', 'value' => $stats['pipeline'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Under review', 'value' => $stats['under_review'], 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Accepted', 'value' => $stats['accepted'], 'accent' => 'violet'])
    </div>

    <x-dash.list-partial-zone>
        @include('dashboard.editor.partials.pipeline-list')
    </x-dash.list-partial-zone>
@endsection
