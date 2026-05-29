<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AdminUserCsvImporter
{
    public const HEADERS = ['Name', 'Email', 'Platform admin', 'Status', 'Journal roles'];

    /**
     * @var array<string, Journal>
     */
    private array $journalsBySubdomain = [];

    public function __construct()
    {
        foreach (Journal::query()->get() as $journal) {
            $this->journalsBySubdomain[$journal->subdomain] = $journal;
        }
    }

    /**
     * @throws ValidationException
     */
    public function import(UploadedFile $file, User $actor): AdminUserImportResult
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => 'Could not read the uploaded CSV file.',
            ]);
        }

        $result = new AdminUserImportResult();
        $header = null;
        $line = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if ($row === [null] || $row === []) {
                continue;
            }

            if ($header === null) {
                $header = $this->normalizeHeader($row);
                $this->assertHeader($header);

                continue;
            }

            try {
                $this->importRow($this->mapRow($header, $row), $actor, $result);
            } catch (\Throwable $exception) {
                $result->errors[] = "Row {$line}: ".$exception->getMessage();
            }
        }

        fclose($handle);

        if ($header === null) {
            throw ValidationException::withMessages([
                'file' => 'The CSV file is empty or missing a header row.',
            ]);
        }

        AdminAuditLogger::log(
            AdminAuditLogger::USER_IMPORTED,
            $actor,
            null,
            [
                'created' => $result->created,
                'updated' => $result->updated,
                'skipped' => $result->skipped,
                'errors' => count($result->errors),
            ],
        );

        return $result;
    }

    /**
     * @param  list<string|null>  $headerRow
     * @return list<string>
     */
    private function normalizeHeader(array $headerRow): array
    {
        $header = array_map(fn ($value) => trim((string) $value), $headerRow);

        if (isset($header[0])) {
            $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
        }

        return $header;
    }

    /**
     * @param  list<string>  $header
     */
    private function assertHeader(array $header): void
    {
        $required = ['Name', 'Email'];

        foreach ($required as $column) {
            if (! in_array($column, $header, true)) {
                throw ValidationException::withMessages([
                    'file' => "Missing required CSV column: {$column}.",
                ]);
            }
        }
    }

    /**
     * @param  list<string>  $header
     * @param  list<string|null>  $row
     * @return array<string, string>
     */
    private function mapRow(array $header, array $row): array
    {
        $mapped = [];

        foreach ($header as $index => $column) {
            $mapped[$column] = trim((string) ($row[$index] ?? ''));
        }

        return $mapped;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function importRow(array $row, User $actor, AdminUserImportResult $result): void
    {
        $email = mb_strtolower($row['Email'] ?? '');

        if ($email === '') {
            $result->skipped++;

            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address \"{$email}\".");
        }

        $name = trim($row['Name'] ?? '');
        [$firstName, $lastName] = $this->splitName($name);
        $isPlatformAdmin = $this->parseBoolean($row['Platform admin'] ?? 'No');
        $isActive = $this->parseStatus($row['Status'] ?? 'Active');
        $assignments = $this->parseJournalRoles($row['Journal roles'] ?? '');

        $existing = User::query()->where('email', $email)->first();

        if ($existing && $existing->id === $actor->id) {
            throw new \InvalidArgumentException('You cannot import changes to your own account.');
        }

        if ($existing && $existing->isPlatformAdmin() && $isPlatformAdmin === false && AdminUserAccountService::isLastPlatformAdmin($existing)) {
            throw new \InvalidArgumentException('Cannot remove platform admin from the last active administrator.');
        }

        DB::transaction(function () use ($existing, $email, $firstName, $lastName, $name, $isPlatformAdmin, $isActive, $assignments, $actor, $result) {
            if ($existing) {
                $existing->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'name' => $name !== '' ? $name : trim($firstName.' '.$lastName),
                    'is_platform_admin' => $isPlatformAdmin,
                    'is_active' => $isActive,
                ]);

                if (! $isActive) {
                    $existing->tokens()->delete();
                }

                AdminUserRoleSynchronizer::syncAssignments($existing, $assignments, $actor->id);
                $result->updated++;
            } else {
                $user = User::query()->create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'name' => $name !== '' ? $name : trim($firstName.' '.$lastName),
                    'email' => $email,
                    'password' => Hash::make(Str::password(32)),
                    'email_verified_at' => now(),
                    'is_platform_admin' => $isPlatformAdmin,
                    'is_active' => $isActive,
                ]);

                AdminUserRoleSynchronizer::syncAssignments($user, $assignments, $actor->id);
                $result->created++;
            }
        });
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            return ['Imported', 'User'];
        }

        $parts = preg_split('/\s+/', $name, 2) ?: [];

        return [
            $parts[0],
            $parts[1] ?? '',
        ];
    }

    private function parseBoolean(string $value): bool
    {
        return in_array(mb_strtolower(trim($value)), ['yes', 'true', '1'], true);
    }

    private function parseStatus(string $value): bool
    {
        return ! in_array(mb_strtolower(trim($value)), ['suspended', 'inactive', 'disabled', 'no', 'false', '0'], true);
    }

    /**
     * @return list<array{journal_id: string, role: JournalRole}>
     */
    private function parseJournalRoles(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        $assignments = [];

        foreach (explode(';', $value) as $segment) {
            $segment = trim($segment);

            if ($segment === '') {
                continue;
            }

            $parts = array_map('trim', explode('·', $segment, 2));

            if (count($parts) !== 2) {
                throw new \InvalidArgumentException("Invalid journal role \"{$segment}\". Expected format: subdomain · Role.");
            }

            [$subdomain, $label] = $parts;
            $journal = $this->journalsBySubdomain[$subdomain] ?? null;

            if (! $journal) {
                throw new \InvalidArgumentException("Unknown journal subdomain \"{$subdomain}\".");
            }

            $role = $this->resolveRoleLabel($label);

            if (! $role) {
                throw new \InvalidArgumentException("Unknown role \"{$label}\".");
            }

            $assignments[] = [
                'journal_id' => $journal->id,
                'role' => $role,
            ];
        }

        return $assignments;
    }

    private function resolveRoleLabel(string $label): ?JournalRole
    {
        foreach (JournalRole::assignable() as $role) {
            if (strcasecmp($role->label(), $label) === 0) {
                return $role;
            }
        }

        return JournalRole::tryFrom(mb_strtolower($label));
    }
}
