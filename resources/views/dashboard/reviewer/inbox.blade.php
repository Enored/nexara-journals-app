@extends('layouts.dashboard', ['activeNav' => 'reviewer-inbox'])

@section('title', 'Review inbox')
@section('pageTitle', 'Review inbox')
@section('pageDescription', 'Reviews assigned to you. Complete them before the deadline.')

@section('content')
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3">
        @include('partials.dashboard.stat-card', ['label' => 'Assigned', 'value' => $stats['active'], 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Completed', 'value' => $stats['completed'], 'accent' => 'violet'])
        @include('partials.dashboard.stat-card', ['label' => 'Overdue', 'value' => $stats['overdue'], 'accent' => 'rose'])
        @include('partials.dashboard.stat-card', ['label' => 'Total', 'value' => $stats['total']])
    </div>

    <x-dash.list-partial-zone>
        @include('dashboard.reviewer.partials.inbox-list')
    </x-dash.list-partial-zone>
@endsection
