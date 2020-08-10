<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnMakeDefaultNewBillTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('childcare', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('education', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('entertainment', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('food', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('gift', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('housing', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('loans', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('personal', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('shopping', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('tax', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });

        Schema::table('travel', function (Blueprint $table) {
           $table->boolean('not_track_amount')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
