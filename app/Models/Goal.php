<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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

}
