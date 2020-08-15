<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedUtilityTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = ['rent', 'mortgage', 'hoa', 'lawn_maintenance', 'online_subscription', 'home_insurance', 'car_insurance', 'car_finance', 'car_lease'];

        foreach ($types as $type) {
            $utility_type = DB::table('utility_types')->where('slug', $type)->first();

            if (!empty($utility_type)) {
                $utilities = DB::table('utilities')->where('utility_type_id', $utility_type->id)->get();

                if (empty($utilities)) {
                    DB::table('utility_types')->find($utility_type->id)->delete();
                } else {
                    Log::error('Removing Utilities: There are ' . $utilities->count() . ' record(s) with the slug ' . $type);
                }
            }
        }
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
