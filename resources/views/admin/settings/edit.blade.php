@extends('layouts.dashboard', ['activeNav' => 'admin-settings'])

@section('title', 'System settings')
@section('pageTitle', 'System settings')
@section('pageDescription', 'Platform-wide configuration')

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'System settings', 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">

            {{-- Branding --}}
            <form method="POST" action="{{ platform_route('admin.settings.branding.update') }}">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Branding</h5>
                    </div>
                    <div class="card-body">
                        {{-- Platform name --}}
                        <x-dash.input
                            label="Platform name"
                            name="platform_name"
                            :value="old('platform_name', $settings->platform_name)"
                            required
                        />
                        <p class="form-text text-muted mt-n2 mb-4">
                            Shown in the dashboard, browser titles, and emails. Acts as the default logo text.
                        </p>

                        {{-- Logo upload (disabled) --}}
                        <div class="mb-3">
                            <label for="logo_file" class="form-label">Website logo</label>
                            <input
                                type="file"
                                id="logo_file"
                                class="form-control"
                                accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                disabled
                            >
                            <p class="form-text text-muted mb-0 mt-1">
                                Logo upload will be available once cloud storage is configured. The logo text below is used as a fallback.
                            </p>
                            @if ($settings->logo_path)
                                <div class="mt-2 p-2 border rounded d-inline-block bg-light">
                                    <img src="{{ asset($settings->logo_path) }}" alt="Current logo" style="max-height: 40px;">
                                </div>
                            @endif
                        </div>

                        {{-- Logo text --}}
                        <div class="mb-3">
                            <label for="logo_text" class="form-label">Logo text</label>
                            <input
                                type="text"
                                name="logo_text"
                                id="logo_text"
                                value="{{ old('logo_text', $settings->logo_text) }}"
                                class="form-control @error('logo_text') is-invalid @enderror"
                                placeholder="{{ $settings->platform_name }}"
                                maxlength="100"
                            >
                            @error('logo_text')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <p class="form-text text-muted mb-0 mt-1">
                                Displayed as the brand name when no logo image is set. Falls back to the platform name if left empty.
                            </p>
                        </div>

                        {{-- Show text alongside logo --}}
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="hidden" name="show_logo_text_with_image" value="0">
                                <input
                                    type="checkbox"
                                    name="show_logo_text_with_image"
                                    value="1"
                                    id="show-logo-text-with-image"
                                    class="form-check-input"
                                    @checked(old('show_logo_text_with_image', $settings->show_logo_text_with_image))
                                >
                                <label class="form-check-label" for="show-logo-text-with-image">
                                    Show logo text alongside image
                                </label>
                            </div>
                            <p class="form-text text-muted mb-0 mt-1">
                                When checked, the logo text is displayed next to the logo image. Useful for reinforcing the brand name.
                            </p>
                        </div>

                        {{-- Favicon --}}
                        <div class="mb-0">
                            <label class="form-label">Favicon</label>
                            <input
                                type="file"
                                class="form-control"
                                accept="image/x-icon,image/png,image/svg+xml"
                                disabled
                            >
                            <p class="form-text text-muted mb-0 mt-1">
                                Favicon upload will be available once cloud storage is configured. The default favicon is used for now.
                            </p>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <x-dash.button type="submit">Save branding</x-dash.button>
                    </div>
                </div>
            </form>

            {{-- General --}}
            <form method="POST" action="{{ platform_route('admin.settings.general.update') }}" id="general-settings-form">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">General</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-0">
                            <div class="form-check">
                                <input type="hidden" name="maintenance_mode" value="0">
                                <input
                                    type="checkbox"
                                    name="maintenance_mode"
                                    value="1"
                                    id="maintenance-mode"
                                    class="form-check-input"
                                    @checked(old('maintenance_mode', $settings->maintenance_mode))
                                >
                                <label class="form-check-label" for="maintenance-mode">
                                    Maintenance mode
                                </label>
                            </div>
                            <p class="form-text text-muted mb-0 mt-1">
                                When enabled, visitors see a maintenance page. Platform admins can still use the site.
                            </p>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <x-dash.button type="submit">Save general</x-dash.button>
                    </div>
                </div>
            </form>

            {{-- Maintenance mode confirmation modal --}}
            <div class="modal fade" id="maintenance-confirm-modal" tabindex="-1" aria-labelledby="maintenance-confirm-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title" id="maintenance-confirm-title">Enable maintenance mode?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-0">
                                This will make the site inaccessible to all visitors. Only platform admins will be able to use the site.
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="maintenance-confirm-btn" disabled>
                                Confirm (<span id="maintenance-countdown">5</span>s)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkbox = document.getElementById('maintenance-mode');
    const modalEl = document.getElementById('maintenance-confirm-modal');
    const confirmBtn = document.getElementById('maintenance-confirm-btn');

    if (!checkbox || !modalEl || !confirmBtn) return;

    const modal = new bootstrap.Modal(modalEl);
    let countdownInterval = null;
    let wasCheckedBefore = checkbox.checked;

    checkbox.addEventListener('change', () => {
        if (checkbox.checked && !wasCheckedBefore) {
            checkbox.checked = false;
            startConfirmation();
        } else {
            wasCheckedBefore = checkbox.checked;
        }
    });

    function startConfirmation() {
        let remaining = 5;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `Confirm (<span id="maintenance-countdown">${remaining}</span>s)`;

        modal.show();

        countdownInterval = setInterval(() => {
            remaining--;
            const cdEl = document.getElementById('maintenance-countdown');
            if (cdEl) cdEl.textContent = remaining;

            if (remaining <= 0) {
                clearInterval(countdownInterval);
                countdownInterval = null;
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm';
            }
        }, 1000);
    }

    confirmBtn.addEventListener('click', () => {
        checkbox.checked = true;
        wasCheckedBefore = true;
        modal.hide();
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = 'Confirm (<span id="maintenance-countdown">5</span>s)';
    });
});
</script>
@endpush
