<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnVehicleTemplateNotTrack extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('vehicle_templates', 'not_track_amount')) {
            Schema::table('vehicle_templates', function (Blueprint $table) {
                $table->dropColumn('not_track_amount');
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
        if (!Schema::hasColumn('vehicle_templates', 'not_track_amount')) {
            Schema::table('vehicle_templates', function (Blueprint $table) {
                $table->tinyInteger('not_track_amount')->default(0);
            });
        }
    }
}
