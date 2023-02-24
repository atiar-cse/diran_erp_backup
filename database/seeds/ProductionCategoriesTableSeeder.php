<?php

use Illuminate\Database\Seeder;

class ProductionCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('production_categories')->truncate();
        DB::table('production_categories')->insert(array (
            0 =>
            array (
                'id' => 1,
                'product_category_name' => 'Finished Goods',
                'product_category_code' => 'FI',
                'description' => NULL,
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-18 09:05:58',
                'updated_at' => '2019-02-24 06:35:14',
            ),
            1 =>
            array (
                'id' => 2,
                'product_category_name' => 'Active Raw Material',
                'product_category_code' => 'AcRM',
                'description' => NULL,
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-02-18 09:05:58',
                'updated_at' => '2019-02-24 06:40:47',
            ),
            2 =>
            array (
                'id' => 3,
                'product_category_name' => 'Additional Raw Material',
                'product_category_code' => 'AdRM',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-02-24 06:41:13',
                'updated_at' => '2019-02-24 06:41:13',
            ),
            3 =>
            array (
                'id' => 4,
                'product_category_name' => 'Glaze Raw Material',
                'product_category_code' => 'GRM',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-02-24 06:41:55',
                'updated_at' => '2019-02-24 06:41:55',
            ),
            4 =>
            array (
                'id' => 5,
                'product_category_name' => 'Packaging Materials',
                'product_category_code' => 'PFI',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-02-24 06:42:51',
                'updated_at' => '2019-02-24 06:42:51',
            ),
            5 =>
            array (
                'id' => 6,
                'product_category_name' => 'Semi Finsh Goods',
                'product_category_code' => 'SeFI',
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2019-02-24 06:43:17',
                'updated_at' => '2019-02-24 06:43:17',
            ),
        ));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
