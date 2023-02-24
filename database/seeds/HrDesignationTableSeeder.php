<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrDesignationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('hr_designations')->truncate();
        DB::table('hr_designations')->insert(
            [
                [
                    'id'=> 1,
                    'designation_name'=>'Marketing Officer',
                    'created_at' => $time
                ],
                [
                    'id'=> 2,
                    'designation_name'=>'Manager',
                    'created_at' => $time
                ],
                [
                    'id'=> 3,
                    'designation_name'=>'Ex.Manager',
                    'created_at' => $time
                ],

            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
