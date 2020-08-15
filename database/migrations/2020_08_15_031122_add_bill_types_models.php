<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillTypesModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('bill_types')->where('slug', 'banks')->update(['model' => 'Bank']);
        DB::table('bill_types')->where('slug', 'childcare')->update(['model' => 'Childcare']);
        DB::table('bill_types')->where('slug', 'credit_cards')->update(['model' => 'CreditCard', 'slug' => 'credit-cards']);
        DB::table('bill_types')->where('slug', 'education')->update(['model' => 'Education']);
        DB::table('bill_types')->where('slug', 'entertainment')->update(['model' => 'Entertainment']);
        DB::table('bill_types')->where('slug', 'food')->update(['model' => 'Food']);
        DB::table('bill_types')->where('slug', 'gift')->update(['model' => 'Gift']);
        DB::table('bill_types')->where('slug', 'housing')->update(['model' => 'Housing']);
        DB::table('bill_types')->where('slug', 'incomes')->update(['model' => 'Income']);
        DB::table('bill_types')->where('slug', 'investments')->update(['model' => 'Investment']);
        DB::table('bill_types')->where('slug', 'loan')->update(['model' => 'Loan']);
        DB::table('bill_types')->where('slug', 'medical')->update(['model' => 'Medical']);
        DB::table('bill_types')->where('slug', 'miscellaneous')->update(['model' => 'Miscellaneous']);
        DB::table('bill_types')->where('slug', 'personal')->update(['model' => 'Personal']);
        DB::table('bill_types')->where('slug', 'shopping')->update(['model' => 'Shopping']);
        DB::table('bill_types')->where('slug', 'subscription')->update(['model' => 'Subscription']);
        DB::table('bill_types')->where('slug', 'tax')->update(['model' => 'Tax']);
        DB::table('bill_types')->where('slug', 'travel')->update(['model' => 'Travel']);
        DB::table('bill_types')->where('slug', 'utilities')->update(['model' => 'Utility']);
        DB::table('bill_types')->where('slug', 'vehicles')->update(['model' => 'Vehicle']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {}
}
