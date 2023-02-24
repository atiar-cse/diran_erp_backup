<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LcCustomDutyCostNameTableSeeder extends Seeder
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
        DB::table('lc_custom_duty_cost_name_entries')->truncate();
        DB::table('lc_custom_duty_cost_name_entries')->insert(
            [
                ['duty_cost_name'=>'Custom Duty','status'=>1,'created_at' => $time],
                ['duty_cost_name'=>'Regulatory Duty','status'=>1,'created_at' => $time],
                ['duty_cost_name'=>'Supplementary Duty','status'=>1,'created_at' => $time],
                ['duty_cost_name'=>'VAT','status'=>1,'created_at' => $time],
                ['duty_cost_name'=>'Advance Income Tax','status'=>1,'created_at' => $time],
                ['duty_cost_name'=>'Advance Trade VAT','status'=>1,'created_at' => $time],
                ['duty_cost_name'=>'DF & VAT','status'=>1,'created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
