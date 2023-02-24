<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrBranchTableSeeder extends Seeder
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
        DB::table('hr_branches')->truncate();
        DB::table('hr_branches')->insert(
            [
                [
                    'id'=> 1,
                    'branch_name'=>'Dhaka',
                    'created_at' => $time
                ],
                [
                    'id'=> 2,
                    'branch_name'=>'Gazipur',
                    'created_at' => $time
                ],
                [
                    'id'=> 3,
                    'branch_name'=>'Uttora',
                    'created_at' => $time
                ],

            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
