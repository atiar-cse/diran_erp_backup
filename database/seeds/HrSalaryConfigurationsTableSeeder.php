<?php

use Illuminate\Database\Seeder;

class HrSalaryConfigurationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('hr_salary_configurations')->delete();
        
        \DB::table('hr_salary_configurations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key_name' => 'basic_salary',
                'salary_head' => 'Basic',
                'salary_type' => 'Gross',
                'percentage' => '50.00',
            ),
            1 => 
            array (
                'id' => 2,
                'key_name' => 'house_rent',
                'salary_head' => 'House Rent',
                'salary_type' => 'Gross',
                'percentage' => '25.00',
            ),
            2 => 
            array (
                'id' => 3,
                'key_name' => 'medical',
                'salary_head' => 'Medical',
                'salary_type' => 'Gross',
                'percentage' => '12.50',
            ),
            3 => 
            array (
                'id' => 4,
                'key_name' => 'transport',
                'salary_head' => 'Transport',
                'salary_type' => 'Gross',
                'percentage' => '12.50',
            ),
        ));
        
        
    }
}