<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MenuPermissionTableSeeder extends Seeder
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
        DB::table('acl_menu_permissions')->truncate();
        DB::table('acl_menu_permissions')->insert(
            [
                ['role_id'=>'1','menu_id'=>'9','created_at' => $time],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
