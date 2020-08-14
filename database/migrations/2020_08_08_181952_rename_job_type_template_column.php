<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameJobTypeTemplateColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('income_templates', function (Blueprint $table) {
            $table->renameColumn('job_type_id', 'income_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('income_templates', function (Blueprint $table) {
            $table->renameColumn('income_type_id', 'job_type_id');
        });
    }
}
