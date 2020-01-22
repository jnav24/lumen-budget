<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Utilities extends Model
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
    protected $table = 'utilities';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(UtilityTypes::class, 'id', 'utility_type_id');
    }
}