<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBudgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('name');
                $table->dateTime('budget_cycle');
                $table->timestamps();
            });

            Schema::table('budgets', function($table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('budget_templates')) {
            Schema::create('budget_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->timestamps();
            });

            Schema::table('budget_templates', function($table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('budget_templates');
    }
}
