<?php

namespace App\Models;

use App\Enums\BudgetType;
use App\Enums\MonthEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    
    use HasUuids;


    protected $fillable = [
        'user_id',
        'detail',
        'nominal',
        'month',
        'year',
        'type',
    ];


    // casting
    public function casts(): array {
        return [
            'month' => MonthEnum::class,
            'type' => BudgetType::class,
        ];
    }


    // define relation
    public function user(): BelongsTo {
        return $this->belongsTo(related: User::class);
    }


}
