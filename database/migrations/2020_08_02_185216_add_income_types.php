<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIncomeTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('income_types')->insert([
            ['name' => 'Bonus', 'slug' => 'bonus', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Commission', 'slug' => 'commission', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Dividends', 'slug' => 'dividends', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Donation', 'slug' => 'donation', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Fees', 'slug' => 'fees', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Gift', 'slug' => 'gift', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Grant', 'slug' => 'grant', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Interest', 'slug' => 'interest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Passive', 'slug' => 'passive', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Portfolio', 'slug' => 'portfolio', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Prizes', 'slug' => 'prizes', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Rental', 'slug' => 'rental', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Sales', 'slug' => 'sales', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Tips', 'slug' => 'tips', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Reimbursement', 'slug' => 'reimbursement', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
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
