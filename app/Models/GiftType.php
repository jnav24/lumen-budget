<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftType extends Model
{
    protected $table = 'gift_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
