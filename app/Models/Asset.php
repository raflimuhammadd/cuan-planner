<?php

namespace App\Models;

use App\Enums\AssetType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
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
            'type' => AssetType::class,
        ];
    }
}
