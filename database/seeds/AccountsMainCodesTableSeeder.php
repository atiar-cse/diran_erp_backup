<?php

use Illuminate\Database\Seeder;

class AccountsMainCodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('accounts_main_codes')->truncate();
        
        \DB::table('accounts_main_codes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'main_code_title' => 'Assets',
                'main_code' => 3,
                'open_status' => 1,
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-14 05:53:25',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'main_code_title' => 'Expense',
                'main_code' => 2,
                'open_status' => 0,
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-14 05:53:25',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'main_code_title' => 'Income',
                'main_code' => 1,
                'open_status' => 0,
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-14 05:53:25',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'main_code_title' => 'Liabilities',
                'main_code' => 4,
                'open_status' => 1,
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-14 05:53:25',
                'updated_at' => NULL,
            ),
        ));
        
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}