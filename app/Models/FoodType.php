<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodType extends Model
{
    protected $table = 'food_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
