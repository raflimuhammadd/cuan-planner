<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NetWorthAsset extends Model
{
    
    use HasUuids;


    protected $fillable = [
        'asset_id',
        'transaction_date',
        'nominal',
    ];
}
