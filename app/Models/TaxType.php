<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    protected $table = 'tax_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
