<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTemplates extends Model
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
        return $this->hasMany('App\Models\BankTemplates', 'budget_template_id', 'id');
    }

    /**
     * Credit Card Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credit_cards()
    {
        return $this->hasMany('App\Models\CreditCardTemplates', 'budget_template_id', 'id');
    }

    /**
     * Investment Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function investments()
    {
        return $this->hasMany('App\Models\InvestmentTemplates', 'budget_template_id', 'id');
    }

    /**
     * Jobs Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobs()
    {
        return $this->hasMany('App\Models\JobTemplates', 'budget_template_id', 'id');
    }

    /**
     * Medical Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medical()
    {
        return $this->hasMany('App\Models\MedicalTemplates', 'budget_template_id', 'id');
    }

    /**
     * Miscellaneous Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function miscellaneous()
    {
        return $this->hasMany('App\Models\MiscellaneousTemplates', 'budget_template_id', 'id');
    }

    /**
     * Utilities Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utilities()
    {
        return $this->hasMany('App\Models\UtilityTemplates', 'budget_template_id', 'id');
    }
}