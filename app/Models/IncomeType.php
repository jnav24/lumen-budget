<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeType extends Model
{
    protected $table = 'income_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
