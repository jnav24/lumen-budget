<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildcareType extends Model
{
    protected $table = 'childcare_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
