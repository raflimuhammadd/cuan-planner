<?php

namespace App\Models;

use App\Observers\GoalObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(GoalObserver::class)]

class Goal extends Model
{

    use HasUuids;


    protected $fillable = [
        'user_id',
        'name',
        'percentage',
        'nominal',
        'monthly_saving',
        'deadline',
        'beginning_balance'
    ];

    // define relation
    public function user(): BelongsTo {
        return $this->belongsTo(related: User::class);
    }

    // define relation
    public function balances(): HasMany {
        return $this->hasMany(related: Balance::class);
    }

    // function scope filter
    public function scopeFilter(Builder $query, array $filters): void 
    {
        $query->when($filters['search'] ?? null, function($query, $search) {
            $query->where('name', 'REGEXP', $search);
        });
    }

    // function sorting
    public function scopeSorting(Builder $query, $sorts):void
    {
        $query->when($sorts['field'] ?? null && $sorts['direction'] ?? null, function($query) use($sorts) {
            $query->orderBy($sorts['field'], $sorts['direction']);
        });
    }


    // calculate balance
    public function calculateBalance(string $user_id): int
    {
        return Balance::query()
            ->where('user_id', $user_id)
            ->where('goal_id', $this->id)
            ->sum('amount');

    }

    // method percentage
    public function calculatePercentage(int $beginning_balance, int $nominal, string $user_id): float
    {
        return round((($beginning_balance + $this->calculateBalance($user_id)) / $nominal) * 100, precision: 2);
    }

}
