<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'vehicle_types';

    /**
     * Hide columns
     *
     * @var array
     */
    protected $hidden= [
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
