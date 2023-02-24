<?php

use Illuminate\Database\Seeder;

class AccountsBankAccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('accounts_bank_accounts')->delete();
        
        \DB::table('accounts_bank_accounts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'accounts_bank_id' => 15,
                'accounts_branch_id' => 1,
                'accounts_bank_accounts_name' => 'Mr. Abdul Hasnat',
                'accounts_bank_accounts_number' => '151223123525',
                'accounts_bank_accounts_date' => '2019-01-09',
                'chart_ac_id' => 1,
                'accounts_bank_accounts_contact_person' => NULL,
                'accounts_bank_accounts_contact_number' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 12:42:43',
                'updated_at' => '2019-01-09 12:42:43',
            ),
            1 => 
            array (
                'id' => 2,
                'accounts_bank_id' => 15,
                'accounts_branch_id' => 1,
                'accounts_bank_accounts_name' => 'Shariar Kabir',
                'accounts_bank_accounts_number' => '1512023544454',
                'accounts_bank_accounts_date' => '2019-01-09',
                'chart_ac_id' => 1,
                'accounts_bank_accounts_contact_person' => NULL,
                'accounts_bank_accounts_contact_number' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 12:43:15',
                'updated_at' => '2019-01-09 12:43:15',
            ),
        ));
        
        
    }
}