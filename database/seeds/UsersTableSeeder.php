<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_name' => 'Super Admin',
                'email' => 'software@iventurebd.com',
                'password' => '$2y$10$nDSqF.huLyMnW74OyyKu1ecKe6ASduL4ygO3lLS8Qk3T6DRmol6Xe',
                'role_id' => 1,
                'user_photo' => NULL,
                'status' => 1,
                'created_by' => 1,
                'modified_by' => 1,
                'remember_token' => NULL,
                'created_at' => '2019-12-04 05:34:45',
                'updated_at' => '2019-12-04 05:34:45',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'user_name' => 'Admin',
                'email' => 'admin@mail.com',
                'password' => '$2y$10$GrA.s6FbVUd2RiSDMWL57.ibIm35hEjkBJbo9kY0iSprJkhA0PEIi',
                'role_id' => 1,
                'user_photo' => NULL,
                'status' => 1,
                'created_by' => NULL,
                'modified_by' => NULL,
                'remember_token' => 'ztfBWSH1aq',
                'created_at' => '2019-12-02 06:53:28',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),            
        ));
    }
}