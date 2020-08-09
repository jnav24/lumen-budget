<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntertainmentType extends Model
{
    protected $table = 'entertainment_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
