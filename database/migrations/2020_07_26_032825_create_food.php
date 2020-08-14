<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFood extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('food')) {
            Schema::create('food', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_id', false, 'unsigned');
                $table->integer('food_type_id', false, 'unsigned');
                $table->string('name');
                $table->string('amount');
                $table->string('confirmation');
                $table->tinyInteger('not_track_amount')->default(0);
                $table->integer('due_date', false, 'unsigned');
                $table->dateTime('paid_date');
                $table->timestamps();
            });

            Schema::table('food', function($table) {
                $table->foreign('food_type_id')
                    ->references('id')
                    ->on('food_types')
                    ->onDelete('cascade');

                $table->foreign('budget_id')
                    ->references('id')
                    ->on('budgets')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('food_templates')) {
            Schema::create('food_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_template_id', false, 'unsigned');
                $table->integer('food_type_id', false, 'unsigned');
                $table->string('name');
                $table->string('amount');
                $table->integer('due_date', false, 'unsigned');
                $table->timestamps();
            });

            Schema::table('food_templates', function($table) {
                $table->foreign('food_type_id')
                    ->references('id')
                    ->on('food_types')
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
        Schema::dropIfExists('food');
        Schema::dropIfExists('food_templates');
    }
}
