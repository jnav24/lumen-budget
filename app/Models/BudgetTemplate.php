<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTemplate extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'budget_templates';

    /**
     * Bank Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function banks()
    {
        return $this->hasMany(BankTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Childcare
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childcare()
    {
        return $this->hasMany(ChildcareTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Credit Card Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credit_cards()
    {
        return $this->hasMany(CreditCardTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Education
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function education()
    {
        return $this->hasMany(EducationTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entertainment()
    {
        return $this->hasMany(EntertainmentTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Food
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function food()
    {
        return $this->hasMany(FoodTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Gift
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gift()
    {
        return $this->hasMany(GiftTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Housing
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function housing()
    {
        return $this->hasMany(HousingTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Investment Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function investments()
    {
        return $this->hasMany(InvestmentTemplates::class, 'budget_template_id', 'id');
    }

    /**
     * Jobs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incomes()
    {
        return $this->hasMany(IncomeTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Loan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loan()
    {
        return $this->hasMany(LoanTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Personal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personal()
    {
        return $this->hasMany(PersonalTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Shopping
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopping()
    {
        return $this->hasMany(ShoppingTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscription()
    {
        return $this->hasMany(SubscriptionTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Tax
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tax()
    {
        return $this->hasMany(TaxTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Travel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function travel()
    {
        return $this->hasMany(TravelTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Medical Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medical()
    {
        return $this->hasMany(MedicalTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Miscellaneous Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function miscellaneous()
    {
        return $this->hasMany(MiscellaneousTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Utilities Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utilities()
    {
        return $this->hasMany(UtilityTemplate::class, 'budget_template_id', 'id');
    }

    /**
     * Vehicles Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles()
    {
        return $this->hasMany(VehicleTemplate::class, 'budget_template_id', 'id');
    }
}
