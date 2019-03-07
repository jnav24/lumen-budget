<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetAggregation extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'budget_aggregation';

    /**
     * Hide columns
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}