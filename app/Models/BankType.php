<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankType extends Model
{
    protected $table = 'bank_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
