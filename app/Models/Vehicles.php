<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicles extends Model
{
    /**
     * Hide columns
     *
     * @var array
     */
    protected $hidden = [
        'budget_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'vehicles';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(VehicleTypes::class, 'id', 'vehicle_type_id');
    }

    /**
     * @return HasOne
     */
    public function userVehicle()
    {
        return $this->hasOne(UserVehicles::class, 'id', 'user_vehicle_id');
    }
}