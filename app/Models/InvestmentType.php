<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentType extends Model
{
    protected $table = 'investment_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
