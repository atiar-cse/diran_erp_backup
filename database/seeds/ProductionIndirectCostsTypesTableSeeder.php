<?php

use Illuminate\Database\Seeder;

class ProductionIndirectCostsTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('production_indirect_costs_types')->delete();
        
        \DB::table('production_indirect_costs_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'indirect_cost_type' => 'Overhead',
                'description' => 'Other expenditure',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-12-02 07:17:53',
                'updated_at' => '2019-12-02 07:17:53',
            ),
            1 => 
            array (
                'id' => 2,
                'indirect_cost_type' => 'LABOUR',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-12-23 04:38:36',
                'updated_at' => '2019-12-23 04:38:36',
            ),
        ));
        
        
    }
}