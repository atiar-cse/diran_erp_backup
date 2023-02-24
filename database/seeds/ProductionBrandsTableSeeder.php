<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductionBrandsTableSeeder extends Seeder
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
        DB::table('production_brands')->truncate();
        DB::table('production_brands')->insert(array (
            0 =>
            array (
                'id' => 1,
                'product_brand_name' => 'Brand 1',
                'description' => NULL,
                'brand_logo' => NULL,
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
