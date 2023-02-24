<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrUnitsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('hr_units')->truncate();
        DB::table('hr_units')->insert(
            [
                ['unit_name' => 'Unit 01', 'address' => NULL, 'status' => 1, 'created_by' => 1, 'updated_by' => NULL, 'deleted_at' => NULL, 'created_at' => $time, 'updated_at' => $time],
                ['unit_name' => 'Unit 02', 'address' => NULL, 'status' => 1, 'created_by' => 1, 'updated_by' => NULL, 'deleted_at' => NULL, 'created_at' => $time, 'updated_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}