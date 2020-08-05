<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CreditCard extends Model
{
    /**
     * Default Attributes
     *
     * @var array
     */
    protected $attributes = [
        'name' => null,
        'limit' => null,
        'last_4' => null,
        'exp_month' => null,
        'exp_year' => null,
        'apr' => null,
        'due_date' => null,
        'credit_card_type_id' => null,
        'amount' => null,
        'paid_date' => null,
        'confirmation' => null,
        'balance' => null,
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
    protected $table = 'credit_cards';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(CreditCardTypes::class, 'id', 'credit_card_type_id');
    }
}