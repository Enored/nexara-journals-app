@if (auth()->check())
    <nav class="flex flex-wrap items-center gap-2 text-sm">
        <a href="{{ platform_route('dashboard') }}" class="rounded px-2 py-1 hover:bg-white hover:text-journal-primary">Overview</a>
        @if (auth()->user()->isPlatformAdmin())
            <a href="{{ platform_route('admin.dashboard') }}" class="rounded px-2 py-1 hover:bg-white hover:text-journal-primary">Admin</a>
        @endif
        @if (auth()->user()->journalUserRoles()->where('role', \App\Enums\JournalRole::Editor)->exists())
            <a href="{{ platform_route('editor.submissions') }}" class="rounded px-2 py-1 hover:bg-white hover:text-journal-primary">Editor</a>
        @endif
        @if (auth()->user()->journalUserRoles()->where('role', \App\Enums\JournalRole::Reviewer)->exists())
            <a href="{{ platform_route('reviewer.inbox') }}" class="rounded px-2 py-1 hover:bg-white hover:text-journal-primary">Reviewer</a>
        @endif
        <a href="{{ platform_route('author.submissions') }}" class="rounded px-2 py-1 hover:bg-white hover:text-journal-primary">Author</a>
    </nav>
@endif
