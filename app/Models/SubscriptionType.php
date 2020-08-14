<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionType extends Model
{
    protected $table = 'subscription_types';

    protected $hidden= [
        'created_at',
        'updated_at',
    ];
}
