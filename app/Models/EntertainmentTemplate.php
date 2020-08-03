<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntertainmentTemplate extends Model
{
    /**
     * Hide columns
     *
     * @var array
     */
    protected $hidden = [
        'budget_template_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'entertainment_templates';
}
