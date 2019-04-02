<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budgets extends Model
{
    /**
     * Hide columns
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Aggregations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aggregations()
    {
        return $this->hasMany(BudgetAggregation::class, 'budget_id', 'id');
    }

    /**
     * Banks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function banks()
    {
        return $this->hasMany(Banks::class, 'budget_id', 'id');
    }

    /**
     * Credit Card
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credit_cards()
    {
        return $this->hasMany(CreditCards::class, 'budget_id', 'id');
    }

    /**
     * Investments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function investments()
    {
        return $this->hasMany(Investments::class, 'budget_id', 'id');
    }

    /**
     * Jobs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobs()
    {
        return $this->hasMany(Jobs::class, 'budget_id', 'id');
    }

    /**
     * Medical
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medical()
    {
        return $this->hasMany(Medical::class, 'budget_id', 'id');
    }

    /**
     * Miscellaneous
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function miscellaneous()
    {
        return $this->hasMany(Miscellaneous::class, 'budget_id', 'id');
    }

    /**
     * Utilities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utilities()
    {
        return $this->hasMany(Utilities::class, 'budget_id', 'id');
    }

    /**
     * Vehicles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicles::class, 'budget_id', 'id');
    }
}