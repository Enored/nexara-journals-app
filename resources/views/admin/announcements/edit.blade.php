@extends('layouts.dashboard', ['activeNav' => 'admin-announcements'])

@section('title', 'Edit announcement')
@section('pageTitle', 'Edit announcement')
@section('pageDescription', $announcement->title)

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Announcements', 'url' => platform_route('admin.announcements.index')],
        ['label' => 'Edit', 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-body">
                    @include('admin.announcements.partials.form', [
                        'announcement' => $announcement,
                        'action' => platform_route('admin.announcements.update', $announcement),
                        'method' => 'PUT',
                        'submitLabel' => 'Save changes',
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
