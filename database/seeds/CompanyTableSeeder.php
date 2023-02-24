<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     *
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('company_infos')->truncate();
        DB::table('company_infos')->insert(
            [
                [   'id'=> 1,
                    'name'=>'Company Name',
                    'phone'=>'255555555',
                    'mobile'=>'01745487542',
                    'email'=>'company@gmail.com',
                    'fax'=>'99999966666',
                    'web'=>'www.company.com',
                    'address'=>'default',
                    'address2'=>'address2',
					'logo'	=> 'Diran_Enterprise_Ltd.png',
					'status' => 1,
					'created_by' => NULL,
					'created_at' => NULL,
					'updated_at' => $time,
                ],
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
