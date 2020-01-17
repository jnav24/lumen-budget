<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Investments extends Model
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
    protected $table = 'investments';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(InvestmentTypes::class, 'id', 'investment_type_id');
    }
}