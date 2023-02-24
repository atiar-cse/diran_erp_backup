<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
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
        DB::table('currencies')->truncate();
        DB::table('currencies')->insert(
            [
                ['name'=>'US Dollars','symbol'=>'$','created_at' => $time],
                ['name'=>'Singapore US Dollars','symbol'=>'$','created_at' => $time],
                ['name'=>'Pounds','symbol'=>'£','created_at' => $time],
                ['name'=>'Euro','symbol'=>'€','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
