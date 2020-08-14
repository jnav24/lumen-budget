<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCardType extends Model
{
    protected $table = 'credit_card_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
