<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('jobs')) {
            Schema::rename('jobs', 'incomes');
        }

        if (Schema::hasTable('job_types')) {
            Schema::rename('job_types', 'income_types');
        }

        if (Schema::hasTable('job_templates')) {
            Schema::rename('job_templates', 'income_templates');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('incomes')) {
            Schema::rename('incomes', 'jobs');
        }

        if (Schema::hasTable('income_types')) {
            Schema::rename('income_types', 'job_types');
        }

        if (Schema::hasTable('income_templates')) {
            Schema::rename('income_templates', 'job_templates');
        }
    }
}
