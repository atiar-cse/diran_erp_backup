<?php

use Illuminate\Database\Seeder;

class ProductionMeasureUnitsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('production_measure_units')->truncate();

        \DB::table('production_measure_units')->insert(array (
            0 => 
            array (
                'id' => 1,
                'measure_unit' => 'Kg',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-18 03:05:57',
                'updated_at' => '2019-02-18 03:05:57',
            ),
            1 => 
            array (
                'id' => 2,
                'measure_unit' => 'Pcs',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-18 03:05:57',
                'updated_at' => '2019-02-18 03:05:57',
            ),
            2 => 
            array (
                'id' => 3,
                'measure_unit' => 'Set',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-23 04:00:53',
                'updated_at' => '2019-02-23 04:00:53',
            ),
            3 => 
            array (
                'id' => 4,
                'measure_unit' => 'Cft',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-23 04:05:24',
                'updated_at' => '2019-02-23 04:55:21',
            ),
            4 => 
            array (
                'id' => 5,
                'measure_unit' => 'Bag',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-23 04:05:29',
                'updated_at' => '2019-02-23 04:55:33',
            ),
            5 => 
            array (
                'id' => 6,
                'measure_unit' => 'NOS',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-23 04:05:29',
                'updated_at' => '2019-02-23 04:55:33',
            ),
        ));
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}