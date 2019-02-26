<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('banks')) {
            Schema::create('banks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('amount');
                $table->integer('bank_type_id', false, 'unsigned');
                $table->integer('budget_id', false, 'unsigned');
                $table->integer('bank_template_id', false, 'unsigned');
                $table->timestamps();
            });

            Schema::table('banks', function($table) {
                $table->foreign('bank_type_id')
                    ->references('id')
                    ->on('bank_types')
                    ->onDelete('cascade');

                $table->foreign('budget_id')
                    ->references('id')
                    ->on('budgets')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('bank_templates')) {
            Schema::create('bank_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('amount');
                $table->integer('bank_type_id', false, 'unsigned');
                $table->integer('budget_template_id', false, 'unsigned');
                $table->timestamps();
            });

            Schema::table('bank_templates', function($table) {
                $table->foreign('bank_type_id')
                    ->references('id')
                    ->on('bank_types')
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
        Schema::dropIfExists('banks');
        Schema::dropIfExists('bank_templates');
    }
}
