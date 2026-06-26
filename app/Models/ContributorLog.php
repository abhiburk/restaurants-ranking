<?php

namespace App\Models;

use App\Enums\ContributorAction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributorLog extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'contributor_logs';

    protected $fillable = [
        'contributor_id',
        'points',
        'action',
        'loggable_id',
        'loggable_type',
        'note',
    ];

    protected $casts = [
        'action' => ContributorAction::class,
        'points' => 'integer',
    ];

    // ── Relations ────────────────────────────────────────────

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(Contributor::class);
    }

    /**
     * Polymorphic: Vote, Submission, Review, etc.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeForAction($query, ContributorAction $action)
    {
        return $query->where('action', $action->value);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopePositive($query)
    {
        return $query->where('points', '>', 0);
    }

    public function scopeNegative($query)
    {
        return $query->where('points', '<', 0);
    }
}