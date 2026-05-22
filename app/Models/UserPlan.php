<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
      protected $guarded  = [
    'id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}
