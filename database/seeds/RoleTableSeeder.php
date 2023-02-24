<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
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
        DB::table('acl_roles')->truncate();
        DB::table('acl_roles')->insert(
            [
                ['role_name' => 'Super Admin','created_at'=>$time],
                ['role_name' => 'Admin','created_at'=>$time],
                ['role_name' => 'HR','created_at'=>$time],
                ['role_name' => 'Accounts','created_at'=>$time],
                ['role_name' => 'IT','created_at'=>$time],
                ['role_name' => 'Procurement','created_at'=>$time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
