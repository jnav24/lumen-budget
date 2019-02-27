<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCardTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('credit_card_types')) {
            Schema::create('credit_card_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('slug');
                $table->timestamps();
            });

            DB::table('credit_card_types')->insert([
                ['name' => 'MasterCard', 'slug' => 'mastercard', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()],
                ['name' => 'Visa', 'slug' => 'visa', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()],
                ['name' => 'Discover', 'slug' => 'discover', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()],
                ['name' => 'American Express', 'slug' => 'american_express', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()],
                ['name' => 'Store', 'slug' => 'store', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_card_types');
    }
}
