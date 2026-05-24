<?php

namespace App\Support;

final class AdminUserImportResult
{
    /** @param  list<string>  $errors */
    public function __construct(
        public int $created = 0,
        public int $updated = 0,
        public int $skipped = 0,
        public array $errors = [],
    ) {}

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    public function processed(): int
    {
        return $this->created + $this->updated + $this->skipped;
    }

    public function summaryMessage(): string
    {
        $parts = array_filter([
            $this->created > 0 ? "{$this->created} created" : null,
            $this->updated > 0 ? "{$this->updated} updated" : null,
            $this->skipped > 0 ? "{$this->skipped} skipped" : null,
        ]);

        $message = $parts === [] ? 'No rows were processed.' : 'Import complete: '.implode(', ', $parts).'.';

        if ($this->hasErrors()) {
            $message .= ' '.count($this->errors).' row(s) had errors.';
        }

        return $message;
    }
}
