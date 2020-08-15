<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateRecordsFromGroceries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $misc = DB::table('miscellaneous')->where('name', 'LIKE', 'Groceries%')->get();
        $type = DB::table('food_types')->where('slug', 'grocery')->first();

        $misc->each(function ($record) use ($type) {
            DB::table('food')->insert([
                'budget_id' => $record->budget_id,
                'food_type_id' => $type->id,
                'name' => $record->name,
                'amount' => $record->amount,
                'due_date' => $record->due_date,
                'paid_date' => $record->paid_date,
                'confirmation' => $record->confirmation,
                'not_track_amount' => $record->not_track_amount ?? 0,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);

            DB::table('miscellaneous')->where('id', $record->id)->delete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {}
}
