<?php

namespace App\Models;

use App\Enums\AssetType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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


    // define relation
    public function user(): BelongsTo {
        return $this->belongsTo(related: User::class);
    }
    
    public function netWorth(): BelongsTo {
        return $this->belongsTo(related: NetWorth::class);
    }

    public function netWorthAssets(): HasMany {
        return $this->hasMany(related: NetWorthAsset::class);
    }
}
