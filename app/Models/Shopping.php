<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Shopping extends Model
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
    protected $table = 'shopping';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(ShoppingType::class, 'id', 'bank_type_id');
    }
}
