@if (\App\Support\Impersonation::isActive())
    @php
        $impersonator = \App\Support\Impersonation::impersonator();
    @endphp
    <div class="alert alert-warning border-0 rounded mb-3 py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2" role="status">
        <span class="d-flex align-items-center gap-2 mb-0">
            <i data-lucide="user-check" class="fs-sm"></i>
            <span>
                You are viewing the platform as <strong>{{ auth()->user()->name }}</strong>
                @if ($impersonator)
                    <span class="text-muted">({{ $impersonator->email }})</span>
                @endif
            </span>
        </span>
        <form method="POST" action="{{ route('admin.impersonation.stop') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-warning">Stop impersonating</button>
        </form>
    </div>
@endif
