<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('loans')) {
            Schema::create('loans', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_id', false, 'unsigned');
                $table->integer('loan_type_id', false, 'unsigned');
                $table->string('name');
                $table->string('amount');
                $table->string('confirmation');
                $table->tinyInteger('not_track_amount')->default(0);
                $table->integer('due_date', false, 'unsigned');
                $table->dateTime('paid_date');
                $table->timestamps();
            });

            Schema::table('loans', function($table) {
                $table->foreign('loan_type_id')
                    ->references('id')
                    ->on('loan_types')
                    ->onDelete('cascade');

                $table->foreign('budget_id')
                    ->references('id')
                    ->on('budgets')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('loan_templates')) {
            Schema::create('loan_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_template_id', false, 'unsigned');
                $table->integer('loan_type_id', false, 'unsigned');
                $table->string('name');
                $table->string('amount');
                $table->integer('due_date', false, 'unsigned');
                $table->timestamps();
            });

            Schema::table('loan_templates', function($table) {
                $table->foreign('loan_type_id')
                    ->references('id')
                    ->on('loan_types')
                    ->onDelete('cascade');

                $table->foreign('budget_template_id')
                    ->references('id')
                    ->on('budget_templates')
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
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_templates');
    }
}
