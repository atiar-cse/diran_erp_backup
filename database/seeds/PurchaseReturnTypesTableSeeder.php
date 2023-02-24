<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurchaseReturnTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('purchase_return_types')->truncate();
        DB::table('purchase_return_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'purchase_return_types_name' => 'Damage',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => $time,
                'updated_at' => $time,
            ),
            1 =>
            array (
                'id' => 2,
                'purchase_return_types_name' => 'Expire',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => $time,
                'updated_at' => $time,
            ),
        ));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
