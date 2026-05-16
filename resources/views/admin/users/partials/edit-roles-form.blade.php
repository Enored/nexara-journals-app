@php
    use App\Enums\JournalRole;
@endphp

<form
    method="POST"
    action="{{ platform_route('admin.users.update-roles', $user) }}"
    class="space-y-4"
    id="user-roles-form"
>
    @csrf
    @method('PUT')

    @foreach ($returnQuery ?? [] as $key => $value)
        <input type="hidden" name="return[{{ $key }}]" value="{{ $value }}">
    @endforeach

    @forelse ($journals as $journal)
        @php
            $row = $existing->get($journal->id, collect());
        @endphp
        <fieldset class="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
            <legend class="px-1 text-sm font-semibold text-slate-900">
                {{ $journal->name }}
                <span class="font-normal text-slate-500">({{ $journal->subdomain }})</span>
            </legend>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2">
                @foreach (JournalRole::cases() as $role)
                    @php
                        $checked = $row->contains(fn ($jur) => $jur->role === $role);
                    @endphp
                    <label class="flex items-center gap-2 text-sm text-slate-800">
                        <input
                            type="checkbox"
                            name="roles[{{ $journal->id }}][{{ $role->value }}]"
                            value="1"
                            @checked(old("roles.{$journal->id}.{$role->value}", $checked))
                            class="dash-checkbox"
                        >
                        {{ str_replace('_', ' ', ucfirst($role->value)) }}
                    </label>
                @endforeach
            </div>
        </fieldset>
    @empty
        <p class="text-sm text-slate-600">No journals exist yet. Create a journal before assigning roles.</p>
    @endforelse
</form>
