<?php

namespace App\Models;

use App\Enums\LiabilityType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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
}
