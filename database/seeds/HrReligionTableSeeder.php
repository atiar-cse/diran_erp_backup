<?php

use Illuminate\Database\Seeder;

class HrReligionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('hr_religion')->delete();
        
        \DB::table('hr_religion')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Islam',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 00:00:00',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 3,
                'title' => 'Hinduism',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 00:00:00',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 4,
                'title' => 'Christianity',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 00:00:00',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 5,
                'title' => 'Buddhism',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 00:00:00',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 6,
                'title' => 'Other Beliefs',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-11-25 00:00:00',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}