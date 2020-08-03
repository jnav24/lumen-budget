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
     * Childcare
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childcare()
    {
        return $this->hasMany(Childcare::class, 'budget_id', 'id');
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
     * Education
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function education()
    {
        return $this->hasMany(Education::class, 'budget_id', 'id');
    }

    /**
     * Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entertainment()
    {
        return $this->hasMany(Entertainment::class, 'budget_id', 'id');
    }

    /**
     * Food
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function food()
    {
        return $this->hasMany(Food::class, 'budget_id', 'id');
    }

    /**
     * Gift
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gift()
    {
        return $this->hasMany(Gift::class, 'budget_id', 'id');
    }

    /**
     * Housing
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function housing()
    {
        return $this->hasMany(Housing::class, 'budget_id', 'id');
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
    public function incomes()
    {
        return $this->hasMany(Income::class, 'budget_id', 'id');
    }

    /**
     * Loan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loan()
    {
        return $this->hasMany(Loan::class, 'budget_id', 'id');
    }

    /**
     * Personal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personal()
    {
        return $this->hasMany(Personal::class, 'budget_id', 'id');
    }

    /**
     * Shopping
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopping()
    {
        return $this->hasMany(Shopping::class, 'budget_id', 'id');
    }

    /**
     * Subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscription()
    {
        return $this->hasMany(Subscription::class, 'budget_id', 'id');
    }

    /**
     * Tax
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tax()
    {
        return $this->hasMany(Tax::class, 'budget_id', 'id');
    }

    /**
     * Travel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function travel()
    {
        return $this->hasMany(Travel::class, 'budget_id', 'id');
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
