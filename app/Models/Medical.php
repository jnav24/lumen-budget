<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Medical extends Model
{
    /**
     * Default Attributes
     *
     * @var array
     */
    protected $attributes = [
        'name' => null,
        'amount' => null,
        'due_date' => null,
        'medical_type_id' => null,
        'paid_date' => null,
        'confirmation' => null,
        'not_track_amount' => null,
        'budget_id' => null,
    ];

    /**
     * Mass assignment
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'amount',
        'due_date',
        'medical_type_id',
        'paid_date',
        'confirmation',
        'not_track_amount',
        'budget_id',
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
    protected $table = 'medical';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(MedicalType::class, 'id', 'medical_type_id');
    }
}
