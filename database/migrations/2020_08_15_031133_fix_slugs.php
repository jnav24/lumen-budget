<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSlugs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('income_types')->where('slug', 'bi_weekly')->update(['slug' => 'bi-weekly']);
        DB::table('income_types')->where('slug', 'semi_monthly')->update(['slug' => 'semi-monthly']);
        DB::table('income_types')->where('slug', 'one_time')->update(['slug' => 'one-time']);
        DB::table('bill_types')->where('slug', 'credit_cards')->update(['slug' => 'credit-cards']);
        DB::table('credit_card_types')->where('slug', 'american_express')->update(['slug' => 'american-express']);
        DB::table('utility_types')->where('slug', 'water_sewer')->update(['slug' => 'water-sewer']);
        DB::table('utility_types')->where('slug', 'home_phone')->update(['slug' => 'home-phone']);
        DB::table('utility_types')->where('slug', 'cable_internet_phone')->update(['slug' => 'cable-internet-phone']);
        DB::table('utility_types')->where('slug', 'cable_internet')->update(['slug' => 'cable-internet']);
        DB::table('utility_types')->where('slug', 'cable_phone')->update(['slug' => 'cable-phone']);
        DB::table('utility_types')->where('slug', 'internet_phone')->update(['slug' => 'internet-phone']);
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
