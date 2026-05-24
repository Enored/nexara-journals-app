<form
    method="POST"
    action="{{ platform_route('admin.users.update-roles', $user) }}"
    id="user-roles-form"
>
    @csrf
    @method('PUT')

    @foreach ($returnQuery ?? [] as $key => $value)
        <input type="hidden" name="return[{{ $key }}]" value="{{ $value }}">
    @endforeach

    <div class="alert alert-light border mb-3">
        Every account can submit manuscripts as an <strong>author</strong>. Assign <strong>reviewer</strong>, <strong>editor</strong>, or <strong>journal admin</strong> per journal below.
    </div>

    @forelse ($journals as $journal)
        @php
            $row = $existing->get($journal->id, collect());
        @endphp
        <fieldset class="card border mb-3">
            <div class="card-header border-light py-2">
                <legend class="float-none w-auto p-0 mb-0 fs-base fw-semibold">
                    {{ $journal->name }}
                    <span class="text-muted fw-normal">({{ $journal->subdomain }})</span>
                </legend>
            </div>
            <div class="card-body pt-2">
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($roles as $role)
                        @php
                            $checked = $row->contains(fn ($jur) => $jur->role === $role);
                            $inputId = "role-{$journal->id}-{$role->value}";
                        @endphp
                        <div class="form-check">
                            <input
                                type="checkbox"
                                name="roles[{{ $journal->id }}][{{ $role->value }}]"
                                value="1"
                                id="{{ $inputId }}"
                                class="form-check-input"
                                @checked(old("roles.{$journal->id}.{$role->value}", $checked))
                            >
                            <label class="form-check-label" for="{{ $inputId }}">{{ $role->label() }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </fieldset>
    @empty
        <p class="text-muted mb-0">No journals exist yet. Create a journal before assigning roles.</p>
    @endforelse
</form>
