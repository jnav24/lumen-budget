<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CreditCards extends Model
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
    protected $table = 'credit_cards';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(CreditCardTypes::class, 'id', 'credit_card_type_id');
    }
}