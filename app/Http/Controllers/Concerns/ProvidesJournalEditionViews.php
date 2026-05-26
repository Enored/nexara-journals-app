<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Journal;

trait ProvidesJournalEditionViews
{
    /**
     * @return array{editionActiveNav: string, editionsParentLabel: string, editionsParentUrl: string}
     */
    protected function editionLayoutContext(Journal $journal): array
    {
        $user = auth()->user();

        if (! $user?->isPlatformAdmin()) {
            return [
                'editionActiveNav' => 'editor-editions',
                'editionsParentLabel' => 'My journals',
                'editionsParentUrl' => platform_route('editor.journals.index'),
            ];
        }

        return [
            'editionActiveNav' => 'admin-journals',
            'editionsParentLabel' => 'Journals',
            'editionsParentUrl' => platform_route('admin.journals.index'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function withEditionLayout(Journal $journal, array $data): array
    {
        return array_merge($data, $this->editionLayoutContext($journal));
    }

}
