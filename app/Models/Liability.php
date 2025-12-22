<?php

namespace App\Models;

use App\Enums\LiabilityType;
use App\Observers\LiabilityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(LiabilityObserver::class)]

class Liability extends Model
{
    
    use HasUuids;


    protected $fillable = [
        'user_id',
        'net_worth_id',
        'detail',
        'goal',
        'type',
    ];


    
    public function casts(): array
    {
        return [
            'type' => LiabilityType::class,
        ];
    }


    // define relation
    public function user(): BelongsTo 
    {
        return $this->belongsTo(related: User::class);
    }

    // define relation
    public function netWorth(): BelongsTo 
    {
        return $this->belongsTo(related: NetWorth::class);
    }

    // define relation
    public function netWorthLiabilities(): HasMany 
    {
        return $this->hasMany(related: NetWorthLiability::class);
    }

    // scope filter
    public function scopeFilter(Builder $query, array $filters):void
    {
        $query->when($filters['search'] ?? null, function($query, $search) {
            $query->whereAny(['detail', 'goal'], 'REGEXP', $search);
        })->when($filters['type'] ?? null, function($query, $type) {
            match($type) {
                'Hutang Jangka Pendek' => $query->where('type', LiabilityType::SHORTTERMDEBT->value),
                'Hutang Jangka Menengah' => $query->where('type', LiabilityType::MIDTERMDEBT->value),
                'Hutang Jangka Panjang' => $query->where('type', LiabilityType::LONGTERMDEBT->value),
            };
        });
    }

    // sorting
    public function scopeSorting(Builder $query, array $sorts): void
    {
        $query->when($sorts['sort'] ?? null && $sorts['direction'] ?? null, function($query) use($sorts) {
            $query->orderBy($sorts['field'], $sorts['direction']);
        });
    }
} 
