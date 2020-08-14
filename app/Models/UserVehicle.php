<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVehicle extends Model
{
    /**
     * Default Attributes
     *
     * @var array
     */
    protected $attributes = [
        'make' => null,
        'model' => null,
        'year' => null,
        'color' => null,
        'license' => null,
        'active' => null,
        'user_id' => null,
    ];

    /**
     * Mass assignment
     *
     * @var array
     */
    protected $fillable = ['make', 'model', 'year', 'color', 'license', 'active', 'user_id'];

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
     * Get User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
