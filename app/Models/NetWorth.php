<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NetWorth extends Model
{
    
    use HasUuids;


    protected $fillable = [
        'user_id',
        'net_worth_goal',
        'current_net_worth',
        'amount_left',
        'transaction_per_month',
        'year',
    ];

}
