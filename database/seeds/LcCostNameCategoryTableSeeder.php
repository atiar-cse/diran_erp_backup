<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LcCostNameCategoryTableSeeder extends Seeder
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
        DB::table('lc_cost_name_categories')->truncate();
        DB::table('lc_cost_name_categories')->insert(
            [
                ['cost_category_name'=>'Custom Expenses','created_at' => $time],
                ['cost_category_name'=>'Port & Jetty','created_at' => $time],
                ['cost_category_name'=>'Jetty Expenses','created_at' => $time],
                ['cost_category_name'=>'Others','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
