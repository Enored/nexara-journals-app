@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@section('title', 'Journals')
@section('pageTitle', 'Journals')
@section('pageDescription', $journalCount . ' of ' . $journalMax . ' journals used')

@section('headerActions')
    @if ($canCreateMoreJournals)
        <x-dash.button :href="platform_route('admin.journals.create')">
            <i data-lucide="plus" class="fs-sm me-1"></i>
            New journal
        </x-dash.button>
    @endif
@endsection

@section('content')
    @if ($canCreateMoreJournals)
        <div class="alert alert-info d-flex align-items-start gap-2 mb-3" role="status">
            <i data-lucide="info" class="flex-shrink-0 mt-1"></i>
            <p class="mb-0">
                You can add {{ $journalMax - $journalCount }} more {{ Str::plural('journal', $journalMax - $journalCount) }}.
            </p>
        </div>
    @else
        <div class="alert alert-warning mb-3" role="status">
            <p class="fw-semibold mb-1">Journal limit reached</p>
            <p class="mb-0">Your plan includes up to {{ $journalMax }} journals. Contact support to add more.</p>
        </div>
    @endif

    <x-dash.list-partial-zone>
        @include('admin.journals.partials.list')
    </x-dash.list-partial-zone>
@endsection
