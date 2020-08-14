<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateIncomeTypeSlugsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('income_types')->where('slug', 'bi_weekly')->update(['slug' => 'bi-weekly']);
        DB::table('income_types')->where('slug', 'semi_monthly')->update(['slug' => 'semi-monthly']);
        DB::table('income_types')->where('slug', 'one_time')->update(['slug' => 'one-time']);
    }
}
