<?php

use Illuminate\Database\Seeder;

class HrEmployeeTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('hr_employee_types')->delete();
        
        DB::table('hr_employee_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'New',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 09:28:24',
                'updated_at' => '2019-11-25 11:04:33',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'rrr',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => '2019-11-25 09:30:57',
                'created_at' => '2019-11-25 09:30:53',
                'updated_at' => '2019-11-25 09:30:57',
            ),
            2 => 
            array (
                'id' => 3,
                'title' => 'Regular',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 11:04:41',
                'updated_at' => '2019-11-25 11:04:41',
            ),
            3 => 
            array (
                'id' => 4,
                'title' => 'Promoted',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 11:04:48',
                'updated_at' => '2019-11-25 11:04:48',
            ),
            4 => 
            array (
                'id' => 5,
                'title' => 'Left',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 11:04:57',
                'updated_at' => '2019-11-25 11:04:57',
            ),
            5 => 
            array (
                'id' => 6,
                'title' => 'Resign',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 11:05:05',
                'updated_at' => '2019-11-25 11:05:05',
            ),
            6 => 
            array (
                'id' => 7,
                'title' => 'Terminated',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 11:05:20',
                'updated_at' => '2019-11-25 11:05:20',
            ),
        ));
        
        
    }
}