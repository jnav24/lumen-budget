<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVehicles extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'user_vehicles';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}