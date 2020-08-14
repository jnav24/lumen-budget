<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Utility extends Model
{
    /**
     * Default Attributes
     *
     * @var array
     */
    protected $attributes = [
        'name' => null,
        'amount' => null,
        'due_date' => null,
        'utility_type_id' => null,
        'paid_date' => null,
        'confirmation' => null,
        'budget_id' => null,
    ];

    /**
     * Mass assignment
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'amount',
        'due_date',
        'utility_type_id',
        'paid_date',
        'confirmation',
        'budget_id',
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
    protected $table = 'utilities';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(UtilityType::class, 'id', 'utility_type_id');
    }
}
