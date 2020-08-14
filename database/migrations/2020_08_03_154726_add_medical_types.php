<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddMedicalTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $id = DB::table('medical_types')->orderBy('id', 'desc')->first()->id;

        DB::table('medical_types')
            ->insert([
                ['name' => 'Preventive', 'slug' => 'preventative', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Doctor Visit', 'slug' => 'doctor-visit', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Insurance', 'slug' => 'insurance', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Copay', 'slug' => 'copay', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Deductible', 'slug' => 'deductible', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Prescription', 'slug' => 'prescription', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Surgery', 'slug' => 'surgery', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Emergency Room', 'slug' => 'emergency-room', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Outstanding Bill', 'slug' => 'outstanding-bill', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Therapy', 'slug' => 'therapy', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Hospital', 'slug' => 'hospital', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Laboratory', 'slug' => 'laboratory', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Over-the-Counter', 'slug' => 'over-the-counter', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]);

        $medicalTypeId = DB::table('medical_types')->where('slug', 'doctor-visit')->first()->id;
        $medicalAll = DB::table('medical')->get();

        foreach ($medicalAll as $med) {
            DB::table('medical')->where('id', $med->id)->update(['medical_type_id' => $medicalTypeId]);
        }

        DB::table('medical_types')->where('id', '<=', $id)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('medical_types')->where('id', '>', 8)->delete();
    }
}
