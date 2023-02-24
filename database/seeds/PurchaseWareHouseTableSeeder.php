<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurchaseWareHouseTableSeeder extends Seeder
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
        DB::table('purchase_ware_houses')->truncate();
        DB::table('purchase_ware_houses')->insert(
            [
                ['id'=>1,'purchase_ware_houses_name'=>'Diran Factory warehoue','purchase_ware_houses_contact_person_name'=>'Mr. Sanvir Hasan','purchase_ware_houses_mobile' => '01552477447','purchase_ware_houses_address' => 'Mirpur','created_at' => $time],
                ['id'=>2,'purchase_ware_houses_name'=>'Diran Factory warehoue Gagipur','purchase_ware_houses_contact_person_name'=>'Mr. Hasnat Ali','purchase_ware_houses_mobile' => '01678457745','purchase_ware_houses_address' => 'Gajipur','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
