<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnMedicalTemplateNotTrack extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('medical_templates', 'not_track_amount')) {
            Schema::table('medical_templates', function (Blueprint $table) {
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
        if (!Schema::hasColumn('medical_templates', 'not_track_amount')) {
            Schema::table('medical_templates', function (Blueprint $table) {
                $table->tinyInteger('not_track_amount')->default(0);
            });
        }
    }
}
