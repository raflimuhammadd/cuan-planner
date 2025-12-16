<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    

    use HasUuids;

    protected $fillable = [
        'user_id',
        'goal_id',
        'amount',
    ];


    // define relation
    public function user(): BelongsTo {
        return $this->belongsTo(related: User::class);
    }

    // define relation
    public function goal(): BelongsTo {
        return $this->belongsTo(related: Goal::class);
    }

    // scope filter
    public function scopeFilter(Builder $query, array $filter): void
    {
        $query->when($filter['search'] ?? null, function($query, $search) {
            $query->where('name', 'REGEXP', $search);
        });
    }


    // scope sorting
    public function scopeSorting(Builder $query, array $sorts): void
    {
        $query->when($sorts['field'] ?? null && $sorts['direction'] ?? null, function($query) use($sorts) {
            $query->orderBy($sorts['field'], $sorts['direction']);
        });
    }
}
