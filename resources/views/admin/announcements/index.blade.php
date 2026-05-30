@extends('layouts.dashboard', ['activeNav' => 'admin-announcements'])

@section('title', 'Announcements')
@section('pageTitle', 'Announcements')
@section('pageDescription', 'Manage global and per-journal announcements shown on journal home pages.')

@section('headerActions')
    <x-dash.button type="button" data-bs-toggle="modal" data-bs-target="#announcement-create-modal">
        <i data-lucide="megaphone" class="fs-sm me-1"></i>
        New announcement
    </x-dash.button>
@endsection

@section('content')
    <x-dash.list-partial-zone>
        @include('admin.announcements.partials.list')
    </x-dash.list-partial-zone>
@endsection

@push('modals')
    {{-- Create --}}
    <div
        class="modal fade"
        id="announcement-create-modal"
        tabindex="-1"
        aria-labelledby="announcement-create-modal-title"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ platform_route('admin.announcements.store') }}" id="announcement-create-form">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="announcement-create-modal-title">New announcement</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Create an announcement for one journal or the whole platform.</p>
                        @include('admin.announcements.partials.fields', [
                            'journals' => $journals,
                            'idPrefix' => 'announcement-create',
                        ])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit (shared; fields populated client-side from the selected row) --}}
    <div
        class="modal fade"
        id="announcement-edit-modal"
        tabindex="-1"
        aria-labelledby="announcement-edit-modal-title"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form
                    method="POST"
                    action="{{ session('announcement_editing_id') ? platform_route('admin.announcements.update', session('announcement_editing_id')) : '' }}"
                    id="announcement-edit-form"
                >
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h4 class="modal-title" id="announcement-edit-modal-title">Edit announcement</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('admin.announcements.partials.fields', [
                            'announcement' => null,
                            'journals' => $journals,
                            'idPrefix' => 'announcement-edit',
                        ])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    @include('admin.announcements.partials.scope-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bootstrapModal = window.bootstrap?.Modal;

            // --- Edit: populate the shared modal from the clicked row's data ---
            const editForm = document.getElementById('announcement-edit-form');
            const editModalEl = document.getElementById('announcement-edit-modal');

            const setField = (id, value) => {
                const el = document.getElementById(id);
                if (el) {
                    el.value = value ?? '';
                }
            };

            const populateEditForm = (data) => {
                editForm.setAttribute('action', data.action || '');
                setField('announcement-edit-scope', data.scope);
                setField('announcement-edit-journal-id', data.journal_id);
                setField('announcement-edit-category', data.category);
                setField('announcement-edit-display-type', data.type);
                setField('announcement-edit-status', data.status);
                setField('announcement-edit-title', data.title);
                setField('announcement-edit-body', data.body);
                setField('announcement-edit-url', data.url);
                setField('announcement-edit-expires-at', data.expires_at);

                // Reflect scope in the journal field visibility.
                const scopeSelect = document.getElementById('announcement-edit-scope');
                scopeSelect?.dispatchEvent(new Event('change', { bubbles: true }));
            };

            document.addEventListener('click', (event) => {
                const btn = event.target.closest('[data-announcement-edit-open]');
                if (!btn || !editForm || !editModalEl || !bootstrapModal) {
                    return;
                }
                event.preventDefault();

                let payload = {};
                try {
                    payload = JSON.parse(btn.dataset.announcement || '{}');
                } catch {
                    payload = {};
                }
                payload.action = btn.dataset.action || '';
                populateEditForm(payload);
                bootstrapModal.getOrCreateInstance(editModalEl).show();
            });

            // --- Reopen the relevant modal after a validation error ---
            @if ($errors->any())
                @php($formContext = session('announcement_form'))
                const targetId = @json($formContext === 'edit' ? 'announcement-edit-modal' : 'announcement-create-modal');
                const errorModal = document.getElementById(targetId);
                if (errorModal && bootstrapModal) {
                    bootstrapModal.getOrCreateInstance(errorModal).show();
                }
            @endif
        });
    </script>
@endpush
