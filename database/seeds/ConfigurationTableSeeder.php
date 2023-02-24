<?php

use Illuminate\Database\Seeder;

class ConfigurationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('configuration')->delete();
        
        \DB::table('configuration')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Software will go under Maintenance from 2:00 - 3:00 pm on Today.',
                'config_key' => 'notice',
                'config_value' => '...',
                'status' => 0,
                'created_at' => '2019-11-24 05:05:34',
                'updated_at' => '2019-11-24 05:05:34',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Calculate Unit Price from Stock In Transaction Log Table since in days',
                'config_key' => 'calc_stock_in_unit_price_since',
                'config_value' => '1 Months',
                'status' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}