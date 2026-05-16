<?php

namespace App\Models;

use App\Enums\JournalRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalUserRole extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'journal_id',
        'role',
        'assigned_by',
    ];

    protected function casts(): array
    {
        return [
            'role' => JournalRole::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
