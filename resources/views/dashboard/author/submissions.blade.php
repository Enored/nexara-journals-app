@extends('layouts.dashboard', ['activeNav' => 'author-submissions'])

@section('title', 'My submissions')
@section('pageTitle', 'My submissions')
@section('pageDescription', 'Track manuscripts and respond to revision requests.')

@section('content')
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3">
        @include('partials.dashboard.stat-card', ['label' => 'Total', 'value' => $stats['total']])
        @include('partials.dashboard.stat-card', ['label' => 'Active', 'value' => $stats['active'], 'hint' => 'In editorial workflow', 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Revision due', 'value' => $stats['revision'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Published', 'value' => $stats['published'], 'accent' => 'violet'])
    </div>

    <div class="alert alert-light border mt-4 mb-0">
        <strong>New submission:</strong>
        visit a journal site at
        <code class="fs-xs">https://{subdomain}.{{ config('journal.base_domain') }}</code>
        and use <em>Submit manuscript</em> while signed in.
    </div>

    <x-dash.list-partial-zone>
        @include('dashboard.author.partials.submissions-list')
    </x-dash.list-partial-zone>
@endsection
