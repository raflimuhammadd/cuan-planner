<?php

namespace App\Models;

use App\Enums\BudgetType;
use App\Enums\MonthEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    
    use HasUuids;

    protected $fillable = [
        'user_id',
        'date',
        'description',
        'nominal',
        'type',
        'type_detail_id',
        'payment_id',
        'notes',
        'month',
        'year',
    ];


    // casting
    public function casts():array
    {
        return [
            'type' => BudgetType::class,
            'month' => MonthEnum::class,
        ];
    }


    // define relation
    public function payment(): BelongsTo {
        return $this->belongsTo(related: Payment::class);
    }

    // define relation
    public function typeDetail(): BelongsTo {
        return $this->belongsTo(related: Budget::class, foreignKey:'type_detail_id', ownerKey: 'id');
    }
}
