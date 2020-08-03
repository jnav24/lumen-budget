<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationTemplate extends Model
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
    protected $table = 'education_templates';
}
