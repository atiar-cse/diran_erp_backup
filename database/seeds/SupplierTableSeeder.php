<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupplierTableSeeder extends Seeder
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
        DB::table('purchase_supplier_entries')->truncate();
        DB::table('purchase_supplier_entries')->insert(
            [
                ['id'=> 1,'purchase_supplier_name'=>'Crystal Trading House','purchase_supplier_id'=>'43011','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Mr. Rashed','purchase_supplier_business_phone' =>'01552414141','purchase_supplier_mobile' =>'01552414141', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>0,'created_at' => $time],
                ['id'=> 2,'purchase_supplier_name'=>'Jamir Engineering','purchase_supplier_id'=>'43012','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Mr. Karim','purchase_supplier_business_phone' =>'01552404040','purchase_supplier_mobile' =>'01552404040', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>0,'created_at' => $time],
                ['id'=> 3,'purchase_supplier_name'=>'G H Electric Co Ltd.','purchase_supplier_id'=>'43013','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Mst. Shirin Ahamed','purchase_supplier_business_phone' =>'01554314141','purchase_supplier_mobile' =>'01554314141', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>0,'created_at' => $time],
                ['id'=> 4,'purchase_supplier_name'=>'Diran Enterprice (Party)','purchase_supplier_id'=>'43014','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Mr. Hasin Halder','purchase_supplier_business_phone' =>'01752414141','purchase_supplier_mobile' =>'01752414141', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>0,'created_at' => $time],
                ['id'=> 5,'purchase_supplier_name'=>'Akota Engineering','purchase_supplier_id'=>'43015','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Trina  Roy','purchase_supplier_business_phone' =>'01567414141','purchase_supplier_mobile' =>'01567414141', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>0,'created_at' => $time],
                ['id'=> 6,'purchase_supplier_name'=>'Al Hossain Engineering','purchase_supplier_id'=>'43016','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Md. Abdul Hamid','purchase_supplier_business_phone' =>'01789652847','purchase_supplier_mobile' =>'01789652847', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>0,'created_at' => $time],
                ['id'=> 7,'purchase_supplier_name'=>'Shafique Reparing','purchase_supplier_id'=>'43017','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Mr. Tutul ','purchase_supplier_business_phone' =>'01552757575','purchase_supplier_mobile' =>'01552757575', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>0,'created_at' => $time],
                ['id'=> 8,'purchase_supplier_name'=>'HAINA (FUZHOU) IMPORT AND EXPORT CO. LTD.','purchase_supplier_id'=>'43018','purchase_supplier_join_date' => $time, 'purchase_supplier_contact_person_name' => 'Mr. Karim ','purchase_supplier_business_phone' =>'01552757575','purchase_supplier_mobile' =>'01552757585', 'purchase_supplier_credit_limit' =>'500000.00','is_importer' =>1,'created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
