<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_vehicles')) {
            Schema::create('user_vehicles', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id', false, 'unsigned');
                $table->string('make');
                $table->string('model');
                $table->string('year', 4);
                $table->string('color');
                $table->string('license');
                $table->tinyInteger('active')->default(1);
                $table->timestamps();
            });

            Schema::table('user_vehicles', function (Blueprint $table) {
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
        Schema::dropIfExists('user_vehicles');
    }
}
