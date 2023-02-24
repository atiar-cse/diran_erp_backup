<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LcCostNameTableSeeder extends Seeder
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
        DB::table('lc_cost_name_entries')->truncate();
        DB::table('lc_cost_name_entries')->insert(
            [
                [
                    'cost_name'=>'Association Fee',
                    'cost_category_id'=>1,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'Pay Order',
                    'cost_category_id'=>1,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'BL Verify',
                    'cost_category_id'=>1,
                    'created_at' => $time
                ],

                [
                    'cost_name'=>'Port Dues',
                    'cost_category_id'=>2,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'Shipping Agent Charges',
                    'cost_category_id'=>2,
                    'created_at' => $time
                ],

                [
                    'cost_name'=>'Collie Charges',
                    'cost_category_id'=>3,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'Service Charges',
                    'cost_category_id'=>3,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'Examination',
                    'cost_category_id'=>3,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'Special Permission',
                    'cost_category_id'=>3,
                    'created_at' => $time
                ],

                [
                    'cost_name'=>'C & F Agency Commision',
                    'cost_category_id'=>4,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'Transportation By Truck CTG. Dhaka',
                    'cost_category_id'=>4,
                    'created_at' => $time
                ],
                [
                    'cost_name'=>'Unloading in the factory Konabari',
                    'cost_category_id'=>4,
                    'created_at' => $time
                ],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
