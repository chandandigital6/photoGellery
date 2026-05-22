<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserUsage extends Model
{
    protected $guarded  = [
    'id'
    ];

    protected $casts = [
        'usage_date' => 'date',
    ];

}   
