<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     *
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('sales_customers')->truncate();
        DB::table('sales_customers')->insert(
            [
                ['id'=> 1,'sales_customer_name'=>'Rural Electrification Board (REB)','sales_customer_id'=>'32051','sales_customer_join_date' => $time, 'sales_customer_contact_person_name' => 'Mr. Rashed','sales_customer_business_phone' =>'01552414141','sales_customer_mobile' =>'01552414141', 'sales_customer_credit_limit' =>'500000.00','created_at' => $time],
                ['id'=> 2,'sales_customer_name'=>'BMDA -Rajshahi','sales_customer_id'=>'32052','sales_customer_join_date' => $time, 'sales_customer_contact_person_name' => 'Mr. Karim','sales_customer_business_phone' =>'01552404040','sales_customer_mobile' =>'01552404040', 'sales_customer_credit_limit' =>'500000.00','created_at' => $time],
                ['id'=> 3,'sales_customer_name'=>'Power Development board','sales_customer_id'=>'32053','sales_customer_join_date' => $time, 'sales_customer_contact_person_name' => 'Mst. Shirin Ahamed','sales_customer_business_phone' =>'01554314141','sales_customer_mobile' =>'01554314141', 'sales_customer_credit_limit' =>'500000.00','created_at' => $time],
                ['id'=> 4,'sales_customer_name'=>'Energypac Engineering Ltd.','sales_customer_id'=>'32054','sales_customer_join_date' => $time, 'sales_customer_contact_person_name' => 'Mr. Hasin Halder','sales_customer_business_phone' =>'01752414141','sales_customer_mobile' =>'01752414141', 'sales_customer_credit_limit' =>'500000.00','created_at' => $time],
                ['id'=> 5,'sales_customer_name'=>'General Electronic Manufexturing co.(G E M Plant)','sales_customer_id'=>'32055','sales_customer_join_date' => $time, 'sales_customer_contact_person_name' => 'Trina  Roy','sales_customer_business_phone' =>'01567414141','sales_customer_mobile' =>'01567414141', 'sales_customer_credit_limit' =>'500000.00','created_at' => $time],
                ['id'=> 6,'sales_customer_name'=>'***','sales_customer_id'=>'32056','sales_customer_join_date' => $time, 'sales_customer_contact_person_name' => 'Md. Abdul Hamid','sales_customer_business_phone' =>'01789652847','sales_customer_mobile' =>'01789652847', 'sales_customer_credit_limit' =>'500000.00','created_at' => $time],
                ['id'=> 7,'sales_customer_name'=>'Techno Venture Ltd.','sales_customer_id'=>'32057','sales_customer_join_date' => $time, 'sales_customer_contact_person_name' => 'Mr. Tutul ','sales_customer_business_phone' =>'01552757575','sales_customer_mobile' =>'01552757575', 'sales_customer_credit_limit' =>'500000.00','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
