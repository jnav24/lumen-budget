<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestmentTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('investment_types')->where('slug', 'ira')->update([
            'name' => 'Traditional IRA',
            'slug' => 'traditional-ira'
        ]);
        DB::table('investment_types')->insert([
            ['name' => 'Roth IRA', 'slug' => 'roth-ira', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SEP IRA', 'slug' => 'sep-ira', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Mutual Funds', 'slug' => 'mutual-funds', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Brokerage', 'slug' => 'brokerage', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
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
