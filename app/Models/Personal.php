<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Personal extends Model
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
    protected $table = 'personal';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(PersonalType::class, 'id', 'bank_type_id');
    }
}
