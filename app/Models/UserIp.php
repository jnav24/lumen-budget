<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIp extends Model
{
    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'user_ips';
}