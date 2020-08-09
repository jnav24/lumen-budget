<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $table = 'loan_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
