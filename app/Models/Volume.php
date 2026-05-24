<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volume extends Model
{
    use HasUuids;

    protected $fillable = [
        'journal_id',
        'number',
        'title',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function editions(): HasMany
    {
        return $this->hasMany(Edition::class);
    }

    public function label(): string
    {
        return $this->title
            ? 'Vol. '.$this->number.' — '.$this->title
            : 'Vol. '.$this->number;
    }
}
