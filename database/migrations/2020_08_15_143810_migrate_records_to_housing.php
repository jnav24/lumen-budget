<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateRecordsToHousing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $utils = ['hoa', 'mortgage'];

        foreach ($utils as $util) {
            $utility_type = DB::table('utility_types')->where('slug', $util)->first();

            if (!empty($utility_type)) {
                $utilities = DB::table('utilities')->where('utility_type_id', $utility_type->id)->get();
                $type = DB::table('housing_types')->where('slug', $util)->first();

                $utilities->each(function ($record) use ($type) {
                    DB::table('housing')->insert([
                        'budget_id' => $record->budget_id,
                        'housing_type_id' => $type->id,
                        'name' => $record->name,
                        'amount' => $record->amount,
                        'due_date' => $record->due_date,
                        'paid_date' => $record->paid_date,
                        'confirmation' => $record->confirmation,
                        'not_track_amount' => $record->not_track_amount ?? 0,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);

                    DB::table('utilities')->where('id', $record->id)->delete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {}
}
