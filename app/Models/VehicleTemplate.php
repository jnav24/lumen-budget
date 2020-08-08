<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleTemplate extends Model
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
        'budget_template_id' => null,
    ];

    /**
     * Mass assignment
     *
     * @var array
     */
    protected $fillable = [
        'mileage',
        'amount',
        'due_date',
        'user_vehicle_id',
        'vehicle_type_id',
        'paid_date',
        'confirmation',
        'not_track_amount',
        'balance',
        'budget_template_id',
    ];

    /**
     * Hide columns
     *
     * @var array
     */
    protected $hidden = [
        'budget_template_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'vehicle_templates';
}
