<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    /**
     * Default Attributes
     *
     * @var array
     */
    protected $attributes = [
        'mileage' => null,
        'amount' => null,
        'due_date' => null,
        'user_vehicle_id' => null,
        'vehicle_type_id' => null,
        'paid_date' => null,
        'confirmation' => null,
        'not_track_amount' => null,
        'balance' => null,
        'budget_id' => null,
    ];

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
