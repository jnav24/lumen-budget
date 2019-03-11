<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_id', false, 'unsigned');
                $table->string('mileage');
                $table->string('amount');
                $table->integer('due_date', false, 'unsigned');
                $table->integer('user_vehicle_id', false, 'unsigned');
                $table->integer('vehicle_type_id', false, 'unsigned');
                $table->dateTime('paid_date');
                $table->string('confirmation');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vehicle_templates')) {
            Schema::create('vehicle_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('budget_template_id', false, 'unsigned');
                $table->string('mileage');
                $table->string('amount');
                $table->integer('due_date', false, 'unsigned');
                $table->integer('user_vehicle_id', false, 'unsigned');
                $table->integer('vehicle_type_id', false, 'unsigned');
                $table->timestamps();
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
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_templates');
    }
}
