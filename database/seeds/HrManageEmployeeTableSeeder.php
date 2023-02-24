<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrManageEmployeeTableSeeder extends Seeder
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
        DB::table('hr_manage_employees')->truncate();
        DB::table('hr_manage_employees')->insert(
            [
                ['id'=> 1,'role_id'=>'1','user_name'=>'alamin','password' => '123456', 'first_name' => 'Mr. Al','last_name' =>'Amin','fingerprint_no' =>'01552414141', 'department_id' =>'1','designation_id' =>'1','work_shift_id' =>'1','monthly_pay_grade_id' =>'1','hourly_pay_grade_id' =>'1','phone' =>'112333333','gender' =>'1','permanent_status' =>'1','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 2,'role_id'=>'1','user_name'=>'user','password' => '123456', 'first_name' => 'Mr. Jaman','last_name' =>'Ali','fingerprint_no' =>'01552414142', 'department_id' =>'2','designation_id' =>'2','work_shift_id' =>'1','monthly_pay_grade_id' =>'2','hourly_pay_grade_id' =>'2','phone' =>'112333333','gender' =>'1','permanent_status' =>'1','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 3,'role_id'=>'1','user_name'=>'hasan','password' => '123456', 'first_name' => 'Mr. Hasan','last_name' =>'Khan','fingerprint_no' =>'01552414143', 'department_id' =>'3','designation_id' =>'3','work_shift_id' =>'1','monthly_pay_grade_id' =>'2','hourly_pay_grade_id' =>'1','phone' =>'112333333','gender' =>'1','permanent_status' =>'1','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 4,'role_id'=>'1','user_name'=>'rahman','password' => '123456', 'first_name' => 'Mr. Abdur','last_name' =>'Rahman','fingerprint_no' =>'01552414144', 'department_id' =>'3','designation_id' =>'3','work_shift_id' =>'1','monthly_pay_grade_id' =>'1','hourly_pay_grade_id' =>'2','phone' =>'017145454544','gender' =>'1','permanent_status' =>'0','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 5,'role_id'=>'1','user_name'=>'kamal','password' => '123456', 'first_name' => 'Mr. Kamal','last_name' =>'Uddin','fingerprint_no' =>'01552414145', 'department_id' =>'3','designation_id' =>'2','work_shift_id' =>'1','monthly_pay_grade_id' =>'2','hourly_pay_grade_id' =>'1','phone' =>'017145454544','gender' =>'1','permanent_status' =>'1','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 6,'role_id'=>'1','user_name'=>'rahim','password' => '123456', 'first_name' => 'Mr. Abdur','last_name' =>'Rahim','fingerprint_no' =>'01552414146', 'department_id' =>'2','designation_id' =>'1','work_shift_id' =>'1','monthly_pay_grade_id' =>'2','hourly_pay_grade_id' =>'2','phone' =>'017145454544','gender' =>'1','permanent_status' =>'0','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 7,'role_id'=>'1','user_name'=>'jamal','password' => '123456', 'first_name' => 'Mr. Jamal','last_name' =>'Uddin','fingerprint_no' =>'01552414147', 'department_id' =>'1','designation_id' =>'3','work_shift_id' =>'1','monthly_pay_grade_id' =>'1','hourly_pay_grade_id' =>'1','phone' =>'017145454544','gender' =>'1','permanent_status' =>'0','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 8,'role_id'=>'1','user_name'=>'masud','password' => '123456', 'first_name' => 'Mr. Tareq','last_name' =>'Masud','fingerprint_no' =>'01552414148', 'department_id' =>'3','designation_id' =>'3','work_shift_id' =>'1','monthly_pay_grade_id' =>'2','hourly_pay_grade_id' =>'2','phone' =>'017145454544','gender' =>'1','permanent_status' =>'1','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 9,'role_id'=>'1','user_name'=>'razzaq','password' => '123456', 'first_name' => 'Mr. Abdur','last_name' =>'Razzaq','fingerprint_no' =>'01552414149', 'department_id' =>'2','designation_id' =>'2','work_shift_id' =>'1','monthly_pay_grade_id' =>'1','hourly_pay_grade_id' =>'1','phone' =>'017145454544','gender' =>'1','permanent_status' =>'0','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 10,'role_id'=>'1','user_name'=>'saifur','password' => '123456', 'first_name' => 'Mr. Saifur','last_name' =>'Rahman','fingerprint_no' =>'015524141410', 'department_id' =>'1','designation_id' =>'1','work_shift_id' =>'1','monthly_pay_grade_id' =>'1','hourly_pay_grade_id' =>'2','phone' =>'017145454544','gender' =>'1','permanent_status' =>'0','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],
                ['id'=> 11,'role_id'=>'1','user_name'=>'mehedi','password' => '123456', 'first_name' => 'Mr. Mehedi','last_name' =>'Hasan','fingerprint_no' =>'015524141411', 'department_id' =>'2','designation_id' =>'3','work_shift_id' =>'1','monthly_pay_grade_id' =>'2','hourly_pay_grade_id' =>'1','phone' =>'017145454544','gender' =>'1','permanent_status' =>'1','date_of_birth' =>$time,'date_of_joining' =>$time,'em_address' =>'Dhaka','created_at' => $time],

            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
