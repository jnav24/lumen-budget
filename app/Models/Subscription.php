<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
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
        'paid_date' => null,
        'confirmation' => null,
        'not_track_amount' => null,
        'subscription_type_id' => null,
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
        'paid_date',
        'confirmation',
        'not_track_amount',
        'subscription_type_id',
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
    protected $table = 'subscriptions';

    /**
     * @return HasOne
     */
    public function type()
    {
        return $this->hasOne(SubscriptionType::class, 'id', 'bank_type_id');
    }
}
