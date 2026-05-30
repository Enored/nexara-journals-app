<?php

namespace App\Support;

use App\Enums\ReviewModel;
use Illuminate\Validation\Rule;

final class JournalProfileRules
{
    /**
     * Validation rules for the journal profile and review-model fields, shared
     * by the admin web controller and the JSON API so both stay in lockstep.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function rules(): array
    {
        return [
            'abbreviation' => ['nullable', 'string', 'max:50'],
            'excerpt' => ['nullable', 'string'],
            'e_issn' => ['nullable', 'string', 'regex:/^\d{4}-\d{3}[\dX]$/'],
            'p_issn' => ['nullable', 'string', 'regex:/^\d{4}-\d{3}[\dX]$/'],
            'doi_prefix' => ['nullable', 'string', 'max:50', 'regex:/^10\.\d{2,}(\/.+)?$/'],
            'review_model' => ['nullable', Rule::enum(ReviewModel::class)],
            'frequency' => ['nullable', 'string', 'max:50'],
            'license_type' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'cover_image_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    /**
     * Default an omitted/empty review model to Single-Blind, preserving the
     * platform's historical behavior.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withReviewModelDefault(array $data): array
    {
        if (empty($data['review_model'])) {
            $data['review_model'] = ReviewModel::SingleBlind->value;
        }

        return $data;
    }

    /**
     * On update, an absent or empty review model must leave the stored value
     * untouched rather than nulling a non-nullable column.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withoutEmptyReviewModel(array $data): array
    {
        if (array_key_exists('review_model', $data) && empty($data['review_model'])) {
            unset($data['review_model']);
        }

        return $data;
    }
}
