<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

}
