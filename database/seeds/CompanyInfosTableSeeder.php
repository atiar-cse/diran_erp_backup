<?php

use Illuminate\Database\Seeder;

class CompanyInfosTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('company_infos')->delete();
        
        \DB::table('company_infos')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Diran Insulator Factory Ltd.',
                'phone' => 2,
                'mobile' => 1711,
                'email' => 'diraninsulator@gmail.com',
                'fax' => '02-9008491',
                'web' => 'www.dirangroup.com',
                'address' => '582/1 Kazipara, Rokeya Sarani,Mirpur-1216',
                'address2' => 'address2',
                'logo' => 'af9b3822d60c339e636f48a71bc4cc3f.png',
                'status' => 1,
                'created_by' => NULL,
                'created_at' => NULL,
                'updated_at' => '2019-10-24 04:18:25',
            ),
        ));
        
        
    }
}