<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedical extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('medical')) {
            Schema::create('medical', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_id', false, 'unsigned');
                $table->string('name');
                $table->string('amount');
                $table->integer('due_date', false, 'unsigned');
                $table->integer('medical_type_id', false, 'unsigned');
                $table->dateTime('paid_date');
                $table->string('confirmation');
                $table->timestamps();
            });

            Schema::table('medical', function($table) {
                $table->foreign('budget_id')
                    ->references('id')
                    ->on('budgets')
                    ->onDelete('cascade');

                $table->foreign('medical_type_id')
                    ->references('id')
                    ->on('medical_types')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('medical_templates')) {
            Schema::create('medical_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_template_id', false, 'unsigned');
                $table->string('name');
                $table->string('amount');
                $table->integer('due_date', false, 'unsigned');
                $table->integer('medical_type_id', false, 'unsigned');
                $table->timestamps();
            });

            Schema::table('medical_templates', function($table) {
                $table->foreign('budget_template_id')
                    ->references('id')
                    ->on('budget_templates')
                    ->onDelete('cascade');

                $table->foreign('medical_type_id')
                    ->references('id')
                    ->on('medical_types')
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
        Schema::dropIfExists('medical');
        Schema::dropIfExists('medical_templates');
    }
}
