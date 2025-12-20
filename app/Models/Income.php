<?php

namespace App\Models;

use App\Enums\MonthEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    
    use HasUuids;

    protected $fillable = [
        'user_id',
        'source_id',
        'date',
        'nominal',
        'notes',
        'month',
        'year',
    ];


    public function casts(): array
    {
        return [
            'month' => MonthEnum::class,
        ];
    }


    // define relation
    public function user(): BelongsTo {
        return $this->belongsTo(related: User::class);
    }

    // define relation
    public function source(): BelongsTo {
        return $this->belongsTo(related: Budget::class, foreignKey: 'source_id', ownerKey: 'id');
    }

    // filter
    public function scopeFilter(Builder $query, array $filters):void
    {
        $query->when($filters['search'] ?? null, function($query, $search) {
            $query->whereAny(['notes', 'month'], 'REGEXP', $search);
        })->when($filters['month'] ?? null, function($query, $month) {
            $query->where('month', $month);
        })->when($filters['year'] ?? null, function($query, $year) {
            $query->where('year', $year);
        });
    }


    // sorting
    public function scopeSorting(Builder $query, array $sorts):void
    {
        $query->when($sorts['field'] ?? null && $sorts['direction'], function($query) use($sorts){
            $query->orderBy($sorts['field'], $sorts['direction']);
        });
    }
}
