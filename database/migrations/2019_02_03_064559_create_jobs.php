<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('amount');
                $table->integer('job_type_id', false, 'unsigned');
                $table->dateTime('initial_pay_date');
                $table->timestamps();
            });

            Schema::table('jobs', function($table) {
                $table->foreign('job_type_id')
                    ->references('id')
                    ->on('job_types')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
