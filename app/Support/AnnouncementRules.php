<?php

namespace App\Support;

use App\Enums\AnnouncementCategory;
use App\Enums\AnnouncementScope;
use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use App\Models\Journal;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class AnnouncementRules
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'scope' => ['required', Rule::enum(AnnouncementScope::class)],
            'journal_id' => ['nullable', 'uuid', 'exists:journals,id'],
            'category' => ['required', Rule::enum(AnnouncementCategory::class)],
            'type' => ['required', Rule::enum(AnnouncementType::class)],
            'status' => ['required', Rule::enum(AnnouncementStatus::class)],
            'title' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'url' => ['nullable', 'url', 'max:2048'],
            'expires_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalize(array $data): array
    {
        $scope = AnnouncementScope::from($data['scope']);

        if ($scope === AnnouncementScope::Global) {
            $data['journal_id'] = null;
        }

        if (empty($data['url'])) {
            $data['url'] = null;
        }

        if (empty($data['expires_at'])) {
            $data['expires_at'] = null;
        } else {
            $data['expires_at'] = self::parseExpiresAt($data['expires_at']);
        }

        return $data;
    }

    public static function parseExpiresAt(mixed $value): Carbon
    {
        $timezone = config('app.timezone');

        if ($value instanceof Carbon) {
            return $value->copy()->timezone($timezone)->startOfMinute();
        }

        return Carbon::parse((string) $value, $timezone)->startOfMinute();
    }

    public static function configureValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $data = $validator->getData();
            $scope = $data['scope'] ?? null;
            $journalId = $data['journal_id'] ?? null;

            if ($scope === AnnouncementScope::PerJournal->value && empty($journalId)) {
                $validator->errors()->add('journal_id', 'Select a journal for per-journal announcements.');
            }

            if ($scope === AnnouncementScope::Global->value && filled($journalId)) {
                $validator->errors()->add('journal_id', 'Global announcements must not be linked to a journal.');
            }
        });
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    public static function journalOptions(): array
    {
        return Journal::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Journal $j) => ['id' => $j->id, 'name' => $j->name])
            ->all();
    }
}
