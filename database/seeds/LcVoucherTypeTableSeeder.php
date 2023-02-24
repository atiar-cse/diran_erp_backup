<?php

use Illuminate\Database\Seeder;

class LcVoucherTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('lc_voucher_type')->delete();
        
        \DB::table('lc_voucher_type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'voucher_type_name' => 'LC Opening Charges',
                'menu_link_db_tbl' => 'lc_opening_sections',
                'transaction_column' => 'lc_no',
                'narration' => 'LC Opening Charges',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:28:17',
                'updated_at' => '2020-02-10 10:58:47',
            ),
            1 => 
            array (
                'id' => 2,
                'voucher_type_name' => 'LC Insurance Charge',
                'menu_link_db_tbl' => 'lc_insurances',
                'transaction_column' => 'lc_insurance_no',
                'narration' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:29:01',
                'updated_at' => '2020-02-10 10:59:45',
            ),
            2 => 
            array (
                'id' => 3,
                'voucher_type_name' => 'LC Margin',
                'menu_link_db_tbl' => 'lc_cf_value_margin_entries',
                'transaction_column' => 'lc_margin_no',
            'narration' => 'LC (PI) Margin Entry',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:30:14',
                'updated_at' => '2020-02-10 11:00:13',
            ),
            3 => 
            array (
                'id' => 4,
            'voucher_type_name' => 'Commercial Invoice (N/A)',
                'menu_link_db_tbl' => 'lc_commercial_invoice_entries',
                'transaction_column' => 'ci_no',
                'narration' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:32:27',
                'updated_at' => '2020-02-10 11:00:33',
            ),
            4 => 
            array (
                'id' => 5,
                'voucher_type_name' => 'LATR Payment',
                'menu_link_db_tbl' => 'lc_latr_entries',
                'transaction_column' => 'bank_latr_no',
                'narration' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:33:28',
                'updated_at' => '2020-02-10 11:00:59',
            ),
            5 => 
            array (
                'id' => 6,
                'voucher_type_name' => 'Custom Duty Charge',
                'menu_link_db_tbl' => 'lc_custom_duty_charge_entries',
                'transaction_column' => 'lc_custom_duty_no',
                'narration' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:34:38',
                'updated_at' => '2020-02-10 11:01:20',
            ),
            6 => 
            array (
                'id' => 7,
                'voucher_type_name' => 'LC Other Charge',
                'menu_link_db_tbl' => 'lc_others_charge_entries',
                'transaction_column' => 'lc_others_charge_no',
                'narration' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:35:14',
                'updated_at' => '2020-02-10 11:01:34',
            ),
            7 => 
            array (
                'id' => 8,
                'voucher_type_name' => 'LC Stock Entry',
                'menu_link_db_tbl' => 'lc_stock_entries',
                'transaction_column' => 'lc_stock_no',
                'narration' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-02-10 09:35:54',
                'updated_at' => '2020-02-10 11:02:08',
            ),
        ));
        
        
    }
}