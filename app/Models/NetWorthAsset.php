<?php

namespace App\Models;

use App\Observers\NetWorthAssetObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(NetWorthAssetObserver::class)]

class NetWorthAsset extends Model
{
    
    use HasUuids;


    protected $fillable = [
        'asset_id',
        'transaction_date',
        'nominal',
    ];


    // define relation
    public function asset(): BelongsTo 
    {
        return $this->belongsTo(related: Asset::class);
    }
}
