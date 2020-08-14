<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('bill_types')->whereIn('slug', ['banks', 'investments', 'jobs'])->update(['save_type' => 1]);
        DB::table('bill_types')->insert([
            ['name' => 'Childcare', 'slug' => 'childcare', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Education', 'slug' => 'education', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Food', 'slug' => 'food', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Gift', 'slug' => 'gift', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Housing', 'slug' => 'housing', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Loan', 'slug' => 'loan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Personal', 'slug' => 'personal', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Shopping', 'slug' => 'shopping', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Subscription', 'slug' => 'subscription', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Tax', 'slug' => 'tax', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Travel', 'slug' => 'travel', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
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
