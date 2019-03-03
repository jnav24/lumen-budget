<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('credit_cards')) {
            Schema::create('credit_cards', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('limit');
                $table->string('last_4');
                $table->string('exp_month');
                $table->string('exp_year');
                $table->integer('apr', false, 'unsigned');
                $table->integer('due_date', false, 'unsigned');
                $table->integer('credit_card_type_id', false, 'unsigned');
                $table->integer('budget_id', false, 'unsigned');
                $table->dateTime('paid_date');
                $table->string('confirmation');
                $table->string('amount');
                $table->string('balance');
                $table->timestamps();
            });

            Schema::table('credit_cards', function($table) {
                $table->foreign('credit_card_type_id')
                    ->references('id')
                    ->on('credit_card_types')
                    ->onDelete('cascade');

                $table->foreign('budget_id')
                    ->references('id')
                    ->on('budgets')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('credit_card_templates')) {
            Schema::create('credit_card_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('limit', false, 'unsigned');
                $table->string('last_4');
                $table->string('exp_month');
                $table->string('exp_year');
                $table->integer('apr', false, 'unsigned');
                $table->integer('due_date', false, 'unsigned');
                $table->integer('credit_card_type_id', false, 'unsigned');
                $table->integer('budget_template_id', false, 'unsigned');
                $table->string('amount');
                $table->string('balance');
                $table->timestamps();
            });

            Schema::table('credit_card_templates', function($table) {
                $table->foreign('credit_card_type_id')
                    ->references('id')
                    ->on('credit_card_types')
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
        Schema::dropIfExists('credit_cards');
        Schema::dropIfExists('credit_card_templates');
    }
}
