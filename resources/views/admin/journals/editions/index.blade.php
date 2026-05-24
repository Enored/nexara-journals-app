@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@section('title', 'Issues & volumes')
@section('pageTitle', 'Issues & volumes')
@section('pageDescription', $journal->name)

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Journals', 'url' => platform_route('admin.journals.index')],
        ['label' => $journal->name, 'aria' => true],
    ]" />
@endsection

@section('content')
    @include('admin.journals.editions.partials.volumes-list', ['journal' => $journal, 'volumes' => $volumes])

    <x-dash.list-partial-zone>
        @include('admin.journals.editions.partials.list')
    </x-dash.list-partial-zone>
@endsection

@push('modals')
    <x-admin.ajax-modal
        id="volume-create-modal"
        title="New volume"
        submit-form="volume-create-form"
        submit-label="Create volume"
    />
    <x-admin.ajax-modal
        id="edition-create-modal"
        title="New issue"
        submit-form="edition-create-form"
        submit-label="Create issue"
    />
@endpush
