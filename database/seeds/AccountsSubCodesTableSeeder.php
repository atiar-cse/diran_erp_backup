<?php

use Illuminate\Database\Seeder;

class AccountsSubCodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('accounts_sub_codes')->truncate();
        
        \DB::table('accounts_sub_codes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'main_code_id' => 1,
                'sub_code_title' => 'Fixed Assets',
                'sub_code' => 31,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:54:50',
                'updated_at' => '2019-05-15 11:54:50',
            ),
            1 => 
            array (
                'id' => 2,
                'main_code_id' => 1,
                'sub_code_title' => 'Current Assets',
                'sub_code' => 32,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:56:54',
                'updated_at' => '2019-05-15 11:56:54',
            ),
            2 => 
            array (
                'id' => 3,
                'main_code_id' => 1,
                'sub_code_title' => 'Preliminery & Pre Production Expenses',
                'sub_code' => 33,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:57:39',
                'updated_at' => '2019-05-15 11:57:39',
            ),
            3 => 
            array (
                'id' => 4,
                'main_code_id' => 1,
                'sub_code_title' => 'Current A/C With Sister Concern',
                'sub_code' => 34,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:58:02',
                'updated_at' => '2019-05-15 11:58:02',
            ),
            4 => 
            array (
                'id' => 5,
                'main_code_id' => 1,
            'sub_code_title' => 'ADVANCE VAT(DEPOSIT)',
                'sub_code' => 35,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:58:31',
                'updated_at' => '2019-05-15 11:58:31',
            ),
            5 => 
            array (
                'id' => 6,
                'main_code_id' => 2,
                'sub_code_title' => 'Trading Expenses',
                'sub_code' => 21,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:58:49',
                'updated_at' => '2019-05-15 11:58:49',
            ),
            6 => 
            array (
                'id' => 7,
                'main_code_id' => 2,
                'sub_code_title' => 'COST OF GOODS SOLD',
                'sub_code' => 22,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:59:31',
                'updated_at' => '2019-05-15 11:59:31',
            ),
            7 => 
            array (
                'id' => 8,
                'main_code_id' => 2,
                'sub_code_title' => 'MOSQUE DONATION',
                'sub_code' => 23,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 11:59:53',
                'updated_at' => '2019-05-15 11:59:53',
            ),
            8 => 
            array (
                'id' => 9,
                'main_code_id' => 3,
                'sub_code_title' => 'SALES & SERVICE',
                'sub_code' => 11,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:00:14',
                'updated_at' => '2019-05-15 12:00:14',
            ),
            9 => 
            array (
                'id' => 10,
                'main_code_id' => 3,
                'sub_code_title' => 'SCRAP SALES',
                'sub_code' => 12,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:00:38',
                'updated_at' => '2019-05-15 12:00:38',
            ),
            10 => 
            array (
                'id' => 11,
                'main_code_id' => 3,
                'sub_code_title' => 'Others Income',
                'sub_code' => 13,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:00:59',
                'updated_at' => '2019-05-15 12:00:59',
            ),
            11 => 
            array (
                'id' => 12,
                'main_code_id' => 4,
                'sub_code_title' => 'AUTHORISED CAPITAL',
                'sub_code' => 41,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:01:18',
                'updated_at' => '2019-05-15 12:01:18',
            ),
            12 => 
            array (
                'id' => 13,
                'main_code_id' => 4,
                'sub_code_title' => 'Current Liabilities',
                'sub_code' => 42,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:01:41',
                'updated_at' => '2019-05-15 12:01:41',
            ),
            13 => 
            array (
                'id' => 14,
                'main_code_id' => 4,
                'sub_code_title' => 'Bills Payable',
                'sub_code' => 43,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:01:59',
                'updated_at' => '2019-05-15 12:01:59',
            ),
            14 => 
            array (
                'id' => 15,
                'main_code_id' => 4,
                'sub_code_title' => 'Accumulated Depreciation',
                'sub_code' => 44,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:02:21',
                'updated_at' => '2019-05-15 12:02:21',
            ),
            15 => 
            array (
                'id' => 16,
                'main_code_id' => 4,
                'sub_code_title' => 'Bank Loan',
                'sub_code' => 45,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:02:43',
                'updated_at' => '2019-05-15 12:02:43',
            ),
            16 => 
            array (
                'id' => 17,
                'main_code_id' => 4,
                'sub_code_title' => 'Suspense Account',
                'sub_code' => 46,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:03:18',
                'updated_at' => '2019-05-15 12:03:18',
            ),
            17 => 
            array (
                'id' => 18,
                'main_code_id' => 4,
                'sub_code_title' => 'PEOPLES CREDIT',
                'sub_code' => 47,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:03:35',
                'updated_at' => '2019-05-15 12:03:35',
            ),
            18 => 
            array (
                'id' => 19,
                'main_code_id' => 4,
                'sub_code_title' => 'SHORT TERM LOAN A/C',
                'sub_code' => 48,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-05-15 12:03:53',
                'updated_at' => '2019-05-15 12:03:53',
            ),
            19 => 
            array (
                'id' => 20,
                'main_code_id' => 2,
            'sub_code_title' => 'TRUCK RUNNING EXP-BEDFORD ( NO-0159)',
                'sub_code' => 0,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-08-27 10:43:49',
                'updated_at' => '2019-08-27 10:43:49',
            ),
        ));
        
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}