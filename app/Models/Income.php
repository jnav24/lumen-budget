<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Income extends Model
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
    protected $table = 'incomes';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(IncomeType::class, 'id', 'income_type_id');
    }
}
