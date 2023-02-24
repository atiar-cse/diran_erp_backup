<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrDepartmentTableSeeder extends Seeder
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
        DB::table('hr_departments')->truncate();
        DB::table('hr_departments')->insert(
            [
                [
                    'id'=> 1,
                    'department_name'=>'Accounting',
                    'created_at' => $time
                ],
                [
                    'id'=> 2,
                    'department_name'=>'Production',
                    'created_at' => $time
                ],
                [
                    'id'=> 3,
                    'department_name'=>'Marketing',
                    'created_at' => $time
                ],

            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
