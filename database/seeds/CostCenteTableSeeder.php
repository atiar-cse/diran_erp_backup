<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CostCenteTableSeeder extends Seeder
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
        DB::table('accounts_cost_centers')->truncate();
        DB::table('accounts_cost_centers')->insert(
            [
                ['id'=>  1,'cost_center_name'=>  'Accounting department','created_at' => $time],
                ['id'=>  2,'cost_center_name' => 'Human resources department','created_at' => $time],
                ['id'=>  3,'cost_center_name' => 'IT department','created_at' => $time],
                ['id'=>  4,'cost_center_name' => 'Maintenance department','created_at' => $time],
                ['id'=>  5,'cost_center_name' => 'Research & development','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
