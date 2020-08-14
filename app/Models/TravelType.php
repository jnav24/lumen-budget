<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelType extends Model
{
    protected $table = 'travel_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
