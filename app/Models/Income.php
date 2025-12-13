<?php

namespace App\Models;

use App\Enums\MonthEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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
}
