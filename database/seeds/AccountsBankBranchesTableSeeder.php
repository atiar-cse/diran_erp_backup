<?php

use Illuminate\Database\Seeder;

class AccountsBankBranchesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('accounts_bank_branches')->truncate();

        DB::table('accounts_bank_branches')->insert(array(
            0 =>
            array (
                'id' => 1,
                'bank_branch_names' => 'Nabisco Branch',
                'bank_branch_codes' => '151',
                'bank_id' => 15,
                'bank_branch_contact_no' => '2342',
                'bank_branch_address' => '34224',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:17:25',
                'updated_at' => '2019-01-09 06:17:25',
            ),
            1 =>
            array (
                'id' => 2,
                'bank_branch_names' => 'Gulshan Branch',
                'bank_branch_codes' => '45646',
                'bank_id' => 15,
                'bank_branch_contact_no' => '21212',
                'bank_branch_address' => '54564',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:21:04',
                'updated_at' => '2019-01-09 06:21:04',
            ),
            2 =>
            array (
                'id' => 3,
                'bank_branch_names' => 'Uttara Branch',
                'bank_branch_codes' => '469463',
                'bank_id' => 15,
                'bank_branch_contact_no' => '845641',
                'bank_branch_address' => '684543',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:21:46',
                'updated_at' => '2019-01-09 06:23:06',
            ),
            3 =>
            array (
                'id' => 4,
                'bank_branch_names' => 'Tejgaon Branch',
                'bank_branch_codes' => '5354',
                'bank_id' => 15,
                'bank_branch_contact_no' => '575445',
                'bank_branch_address' => '778643',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:22:49',
                'updated_at' => '2019-01-09 06:23:17',
            ),
            4 =>
            array (
                'id' => 5,
                'bank_branch_names' => 'Banani',
                'bank_branch_codes' => '66353',
                'bank_id' => 15,
                'bank_branch_contact_no' => '4530',
                'bank_branch_address' => '456413',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:24:40',
                'updated_at' => '2019-01-09 06:24:40',
            ),
            5 =>
            array (
                'id' => 6,
                'bank_branch_names' => 'Dhanmondi Branch',
                'bank_branch_codes' => '565',
                'bank_id' => 15,
                'bank_branch_contact_no' => '6453',
                'bank_branch_address' => '65465',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:25:38',
                'updated_at' => '2019-01-09 06:25:38',
            ),
            6 =>
            array (
                'id' => 7,
                'bank_branch_names' => 'Farmgate Branch',
                'bank_branch_codes' => '5645',
                'bank_id' => 15,
                'bank_branch_contact_no' => '6541',
                'bank_branch_address' => '564654',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:26:37',
                'updated_at' => '2019-01-09 06:26:37',
            ),
            7 =>
            array (
                'id' => 8,
                'bank_branch_names' => 'MOTIJHEEL BRANCH',
                'bank_branch_codes' => '654651',
                'bank_id' => 15,
                'bank_branch_contact_no' => '4651',
                'bank_branch_address' => '65460',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:27:27',
                'updated_at' => '2019-01-09 06:27:27',
            ),
            8 =>
            array (
                'id' => 9,
                'bank_branch_names' => 'Mirpur Branch',
                'bank_branch_codes' => '54654',
                'bank_id' => 15,
                'bank_branch_contact_no' => '6545',
                'bank_branch_address' => '654545',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:29:11',
                'updated_at' => '2019-01-09 06:29:11',
            ),
            9 =>
            array (
                'id' => 10,
                'bank_branch_names' => 'Baridhara Branch',
                'bank_branch_codes' => '46546',
                'bank_id' => 15,
                'bank_branch_contact_no' => '46846',
                'bank_branch_address' => '56465',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-01-09 06:30:09',
                'updated_at' => '2019-01-09 06:31:35',
            ),
        ));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
