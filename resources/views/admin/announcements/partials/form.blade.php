@props([
    'announcement' => null,
    'action',
    'method' => 'POST',
    'submitLabel' => 'Save announcement',
    'journals' => [],
])

@php
    use App\Enums\AnnouncementCategory;
    use App\Enums\AnnouncementScope;
    use App\Enums\AnnouncementStatus;
    use App\Enums\AnnouncementType;

    $scope = old('scope', $announcement?->scope?->value ?? AnnouncementScope::Global->value);
    $journalId = old('journal_id', $announcement?->journal_id);
    $category = old('category', $announcement?->category?->value ?? AnnouncementCategory::Editorial->value);
    $displayType = old('type', $announcement?->type?->value ?? AnnouncementType::Info->value);
    $statusValue = old('status', $announcement?->status?->value ?? AnnouncementStatus::Draft->value);
    $title = old('title', $announcement?->title);
    $body = old('body', $announcement?->body);
    $url = old('url', $announcement?->url);
    $expiresAt = old(
        'expires_at',
        $announcement?->expires_at?->timezone(config('app.timezone'))->format('Y-m-d\TH:i'),
    );
    $expiresAtHelp = config('app.timezone');
@endphp

<form method="POST" action="{{ $action }}" id="announcement-form">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <x-dash.select label="Scope" name="scope" id="announcement-scope" required>
        @foreach (AnnouncementScope::cases() as $option)
            <option value="{{ $option->value }}" @selected($scope === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </x-dash.select>

    <div id="announcement-journal-group" class="mb-3">
        <x-dash.select label="Journal" name="journal_id" id="announcement-journal-id">
            <option value="">Select a journal…</option>
            @foreach ($journals as $journal)
                <option value="{{ $journal['id'] }}" @selected($journalId === $journal['id'])>{{ $journal['name'] }}</option>
            @endforeach
        </x-dash.select>
        <div class="form-text">Required when scope is per-journal. Global announcements appear on every journal home page.</div>
        @error('journal_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <x-dash.select label="Category" name="category" id="announcement-category" required>
        @foreach (AnnouncementCategory::cases() as $option)
            <option value="{{ $option->value }}" @selected($category === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </x-dash.select>

    <x-dash.select label="Display type" name="type" id="announcement-display-type" required>
        @foreach (AnnouncementType::cases() as $option)
            <option value="{{ $option->value }}" @selected($displayType === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </x-dash.select>
    <div class="form-text mb-3" style="margin-top: -0.5rem;">Controls badge colour on the public journal page (info, warning, success).</div>

    <x-dash.select label="Status" name="status" id="announcement-status" required>
        @foreach (AnnouncementStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected($statusValue === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </x-dash.select>
    <div class="form-text mb-3" style="margin-top: -0.5rem;">Only <strong>Published</strong> announcements appear on journal home pages (and only before their expiry date, if set).</div>

    <x-dash.input label="Title" name="title" :value="$title" required maxlength="500" />

    <x-dash.textarea label="Body" name="body" rows="8" required>{{ $body }}</x-dash.textarea>
    <div class="form-text mb-3" style="margin-top: -0.5rem;">Separate paragraphs with a blank line.</div>

    <x-dash.input label="Link URL (optional)" name="url" type="text" :value="$url" placeholder="https://…" maxlength="2048" inputmode="url" />

    <x-dash.input label="Expires at (optional)" name="expires_at" id="announcement-expires-at" type="datetime-local" :value="$expiresAt" />
    <div class="form-text mb-3" style="margin-top: -0.5rem;">After this time the announcement hides automatically from public journal pages. Enter the date and time in <strong>{{ $expiresAtHelp }}</strong>.</div>

    <x-dash.form-actions>
        <x-dash.button type="submit">{{ $submitLabel }}</x-dash.button>
        <x-dash.button variant="secondary" :href="platform_route('admin.announcements.index')">Cancel</x-dash.button>
    </x-dash.form-actions>
</form>
