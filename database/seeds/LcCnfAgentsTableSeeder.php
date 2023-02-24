<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LcCnfAgentsTableSeeder extends Seeder
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
        DB::table('lc_cnf_agents')->truncate();
        DB::table('lc_cnf_agents')->insert(
            [
                ['cnf_agent_name'=>'Ahmed and Company','created_at' => $time],
                ['cnf_agent_name'=>'A S Corporation','created_at' => $time],
                ['cnf_agent_name'=>'A. R. Traders','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
