<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NetWorth extends Model
{
    
    use HasUuids;


    protected $fillable = [
        'user_id',
        'net_worth_goal',
        'current_net_worth',
        'amount_left',
        'transaction_per_month',
        'year',
    ];


    // define relation
    public function user(): BelongsTo 
    {
        return $this->belongsTo(related: User::class);
    }

    // define relation
    public function assets(): HasMany 
    {
        return $this->hasMany(related: Asset::class);
    }

    // define relation
    public function liabilities(): HasMany 
    {
        return $this->hasMany(related: Liability::class);
    }

    public function scopeFilter(Builder $query, array $filters):void
    {
        $query->when($filters['search'] ?? null, function($query, $search) {
            $query->whereAny([
                'net_worth_goal',
                'current_net_worth',
                'amount_left'
            ], 'REGEXP', $search);
        });
    }

    public function scopeSorting(Builder $query, array $sorts):void
    {
        $query->when($sorts['field'] ?? null && $sorts['direction'] ?? null, function($query) use($sorts) {
            $query->orderBy($sorts['field'], $sorts['direction']);
        });
    }
}
