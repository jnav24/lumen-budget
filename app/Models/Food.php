<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Food extends Model
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
    protected $table = 'food';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(FoodType::class, 'id', 'bank_type_id');
    }
}
