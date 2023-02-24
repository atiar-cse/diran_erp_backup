<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Seeder
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
        DB::table('acl_modules')->truncate();
        DB::table('acl_modules')->insert(
            [
                ['id'=>  1,'module_name'=>  'Administration','icon_class'=>'flaticon-users','created_at' => $time],
                ['id'=>  2,'module_name' => 'Production','icon_class' => 'flaticon-share','created_at' => $time],
                ['id'=>  3,'module_name' => 'Purchase','icon_class' => 'flaticon-bag','created_at' => $time],
                ['id'=>  4,'module_name' => 'Sales','icon_class' => 'flaticon-truck','created_at' => $time],
                ['id'=>  5,'module_name' => 'Inventory','icon_class' => 'flaticon-open-box','created_at' => $time],
                ['id'=>  6,'module_name' => 'Accounts','icon_class' => 'flaticon-suitcase','created_at' => $time],
                ['id'=>  7,'module_name' => 'LC','icon_class' => 'flaticon-list','created_at' => $time],
                ['id'=>  8,'module_name' => 'HR','icon_class' => 'flaticon-network','created_at' => $time],
                ['id'=>  9,'module_name' => 'Payroll','icon_class' => 'flaticon-coins','created_at' => $time],
                ['id'=>  111,'module_name' => 'Advanced','icon_class' => 'flaticon-cogwheel','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
