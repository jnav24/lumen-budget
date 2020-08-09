<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingType extends Model
{
    protected $table = 'shopping_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
