@extends('layouts.dashboard', ['activeNav' => 'reviewer-inbox'])

@section('title', 'Review inbox')
@section('pageTitle', 'Review inbox')
@section('pageDescription', 'Invitations and reviews that need your attention. Completed work stays out of the default list.')

@section('content')
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3">
        @include('partials.dashboard.stat-card', ['label' => 'Invitations', 'value' => $stats['invited'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'In progress', 'value' => $stats['active'], 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Completed', 'value' => $stats['completed'], 'accent' => 'violet'])
        @include('partials.dashboard.stat-card', ['label' => 'Overdue', 'value' => $stats['overdue'], 'accent' => 'rose'])
    </div>

    <x-dash.list-partial-zone>
        @include('dashboard.reviewer.partials.inbox-list')
    </x-dash.list-partial-zone>
@endsection
