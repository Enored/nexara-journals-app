@extends('layouts.dashboard', ['activeNav' => 'admin-announcements'])

@section('title', 'New announcement')
@section('pageTitle', 'New announcement')
@section('pageDescription', 'Create an announcement for one journal or the whole platform.')

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Announcements', 'url' => platform_route('admin.announcements.index')],
        ['label' => 'New announcement', 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-body">
                    @include('admin.announcements.partials.form', [
                        'action' => platform_route('admin.announcements.store'),
                        'method' => 'POST',
                        'submitLabel' => 'Create announcement',
                        'journals' => $journals,
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('admin.announcements.partials.scope-script')
@endpush
