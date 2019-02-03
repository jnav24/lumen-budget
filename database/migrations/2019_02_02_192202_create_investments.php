<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('investments')) {
            Schema::create('investments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('amount');
                $table->integer('investment_type_id', false, 'unsigned');
                $table->integer('budget_id', false, 'unsigned');
                $table->timestamps();
            });

            Schema::table('investments', function($table) {
                $table->foreign('investment_type_id')
                    ->references('id')
                    ->on('investment_types')
                    ->onDelete('cascade');

                $table->foreign('budget_id')
                    ->references('id')
                    ->on('budgets')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('investment_templates')) {
            Schema::create('investment_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('amount');
                $table->integer('investment_type_id', false, 'unsigned');
                $table->integer('budget_template_id', false, 'unsigned');
                $table->timestamps();
            });

            Schema::table('investment_templates', function($table) {
                $table->foreign('investment_type_id')
                    ->references('id')
                    ->on('investment_types')
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
        Schema::dropIfExists('investments');
        Schema::dropIfExists('investment_templates');
    }
}
