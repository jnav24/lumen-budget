<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalType extends Model
{
    protected $table = 'medical_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
