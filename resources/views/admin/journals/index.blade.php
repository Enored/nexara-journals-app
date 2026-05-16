@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@section('title', 'Journals')
@section('pageTitle', 'Journals')
@section('pageDescription', $journalCount . ' of ' . $journalMax . ' journals used')

@section('content')
  @if ($canCreateMoreJournals)
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <p class="text-sm text-slate-600">
        You can add {{ $journalMax - $journalCount }} more {{ Str::plural('journal', $journalMax - $journalCount) }}.
      </p>
      <x-dash.button :href="platform_route('admin.journals.create')">New journal</x-dash.button>
    </div>
  @else
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950" role="status">
      <p class="font-medium">Journal limit reached</p>
      <p class="mt-1 text-amber-900/90">Your plan includes up to {{ $journalMax }} journals. Contact support to add more.</p>
    </div>
  @endif

  <x-dash.table>
    <x-slot:header>
      <tr>
        <th>Name</th>
        <th>Subdomain</th>
        <th>ISSN</th>
        <th>Status</th>
        <th class="text-right">Actions</th>
      </tr>
    </x-slot:header>
    <x-slot:body>
      @forelse ($journals as $journal)
        <tr>
          <td class="font-medium text-slate-900">{{ $journal->name }}</td>
          <td class="font-mono text-xs text-slate-600">{{ $journal->subdomain }}</td>
          <td class="text-slate-600">{{ $journal->issn ?? '—' }}</td>
          <td>
            @if ($journal->is_active)
              <x-dash.badge class="bg-emerald-50 text-emerald-800">Active</x-dash.badge>
            @else
              <x-dash.badge class="bg-slate-100 text-slate-500">Inactive</x-dash.badge>
            @endif
          </td>
          <td class="text-right whitespace-nowrap">
            <x-dash.link :href="platform_route('admin.journals.edit', $journal)">Edit</x-dash.link>
            <span class="text-slate-300">·</span>
            <x-dash.link :href="platform_route('admin.journals.editions.index', $journal)">Issues</x-dash.link>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="!p-0">
            <x-dash.empty title="No journals yet" description="Create your first journal to launch a subdomain site.">
              @if ($canCreateMoreJournals)
                <x-dash.button :href="platform_route('admin.journals.create')">New journal</x-dash.button>
              @endif
            </x-dash.empty>
          </td>
        </tr>
      @endforelse
    </x-slot:body>
  </x-dash.table>

  <x-dash.pagination :paginator="$journals" />
@endsection
