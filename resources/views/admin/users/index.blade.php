@extends('layouts.dashboard', ['activeNav' => 'admin-users'])

@section('title', 'Users')
@section('pageTitle', 'Users')
@section('pageDescription', 'Search users, manage account status, import or export CSV, and assign staff roles.')

@section('content')
    @if (session('import_errors'))
        <div class="alert alert-danger mb-3" role="alert">
            <p class="fw-medium mb-2">Some rows could not be imported:</p>
            <ul class="mb-0 ps-3">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-dash.list-partial-zone>
        @include('admin.users.partials.list')
    </x-dash.list-partial-zone>
@endsection

@push('modals')
    <x-admin.ajax-modal
        id="user-roles-modal"
        title="Edit staff roles"
        size="modal-lg"
        submit-form="user-roles-form"
        submit-label="Save roles"
    />

    <div class="modal fade" id="user-import-modal" tabindex="-1" aria-labelledby="user-import-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form
                    method="POST"
                    action="{{ platform_route('admin.users.import') }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @foreach (\App\Support\AdminUserIndexFilters::queryParamsFromRequest(request()) as $key => $value)
                        <input type="hidden" name="return[{{ $key }}]" value="{{ $value }}">
                    @endforeach
                    <div class="modal-header">
                        <h4 class="modal-title" id="user-import-modal-title">Import users from CSV</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Upload a CSV exported from this page. Required columns: <strong>Name</strong>, <strong>Email</strong>.
                            Optional: Platform admin, Status, Journal roles.
                        </p>
                        <div class="mb-0">
                            <label for="user-import-file" class="form-label">CSV file</label>
                            <input
                                type="file"
                                name="file"
                                id="user-import-file"
                                class="form-control"
                                accept=".csv,text/csv,text/plain"
                                required
                            >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import users</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
