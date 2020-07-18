<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('bank_types')->insert([
            ['name' => 'Money Market Account (MMA)', 'slug' => 'mma', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()],
            ['name' => 'Certificate of Deposit (CD)', 'slug' => 'cd', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()],
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
