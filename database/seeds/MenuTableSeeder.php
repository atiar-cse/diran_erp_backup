<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
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
        DB::table('acl_menus')->truncate();
        DB::table('acl_menus')->insert(
            [
                //Company Info
                //array('id'=>0,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Company Info', 'menu_url'  => 'Company-Info.index', 'module_id'  => '1','created_at' => $time,'status' => 1),

                //User Section
                array('id'=>1,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Users', 'menu_url'  => 'user.index', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>2,'parent_id' => 1,'action'=> 1,'menu_name'  => 'Add', 'menu_url'  => 'user.create', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>3,'parent_id' => 1,'action'=> 1,'menu_name'  => 'Edit', 'menu_url'  => 'user.edit', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>4,'parent_id' => 1,'action'=> 1,'menu_name'  => 'Delete', 'menu_url'  => 'user.destroy', 'module_id'  => '1','created_at' => $time,'status' => 1),

                //Role Section
                array('id'=>5,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Roles', 'menu_url'  => 'role.index', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>6,'parent_id' => 5,'action'=> 5,'menu_name'  => 'Add', 'menu_url'  => 'role.create', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>7,'parent_id' => 5,'action'=> 5,'menu_name'  => 'Edit', 'menu_url'  => 'role.edit', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>8,'parent_id' => 5,'action'=> 5,'menu_name'  => 'Delete', 'menu_url'  => 'role.destroy', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>9,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'User Role Permission', 'menu_url'  => 'user-role-permissoin.index', 'module_id'  => '1','created_at' => $time,'status' => 1),
                array('id'=>10,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Company Info', 'menu_url'  => 'Company-Info.index', 'module_id'  => '1','created_at' => $time,'status' => 1),



                //Production Section
                array('id'=>100,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Setup', 'menu_url'  => NULL, 'module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>110,'parent_id' => 100,'action'=> NULL,'menu_name'  => 'Product Entry', 'menu_url'  => 'production-entry.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>111,'parent_id' => 110,'action'=> 110,'menu_name'  => 'Store', 'menu_url'  => 'production-entry.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>112,'parent_id' => 110,'action'=> 110,'menu_name'  => 'Edit', 'menu_url'  => 'production-entry.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>113,'parent_id' => 110,'action'=> 110,'menu_name'  => 'Delete', 'menu_url'  => 'production-entry.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>120,'parent_id' => 100,'action'=> NULL,'menu_name'  => 'Product Category', 'menu_url'  => 'production-category.create', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>130,'parent_id' => 100,'action'=> NULL,'menu_name'  => 'Product Brand', 'menu_url'  => 'production-brand.create', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>140,'parent_id' => 100,'action'=> NULL,'menu_name'  => 'Measure Unit', 'menu_url'  => 'measure-unit.create', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>150,'parent_id' => 100,'action'=> NULL,'menu_name'  => 'Raw Material Ratio Setup', 'menu_url'  => 'raw-material-ratio-setup.index', 'module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>160,'parent_id' => 100,'action'=> NULL,'menu_name'  => 'Indirect Costs Type', 'menu_url'  => 'indirect-costs-type.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>161,'parent_id' => 160,'action'=> 160,'menu_name'  => 'Edit', 'menu_url'  => 'indirect-costs-type.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>162,'parent_id' => 160,'action'=> 160,'menu_name'  => 'Delete', 'menu_url'  => 'indirect-costs-type.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>200,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Requisition', 'menu_url'  => 'requisition-for-raw-material.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>201,'parent_id' => 200,'action'=> 200,'menu_name'  => 'Store', 'menu_url'  => 'requisition-for-raw-material.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>202,'parent_id' => 200,'action'=> 200,'menu_name'  => 'Edit', 'menu_url'  => 'requisition-for-raw-material.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>203,'parent_id' => 200,'action'=> 200,'menu_name'  => 'View', 'menu_url'  => 'requisition-for-raw-material.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>204,'parent_id' => 200,'action'=> 200,'menu_name'  => 'Delete', 'menu_url'  => 'requisition-for-raw-material.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>205,'parent_id' => 200,'action'=> 200,'menu_name'  => 'Approve', 'menu_url'  => 'requisition-for-raw-material.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>210,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Massbody Section', 'menu_url'  => 'massbody-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>211,'parent_id' => 210,'action'=> 210,'menu_name'  => 'Store', 'menu_url'  => 'massbody-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>212,'parent_id' => 210,'action'=> 210,'menu_name'  => 'Edit', 'menu_url'  => 'massbody-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>213,'parent_id' => 210,'action'=> 210,'menu_name'  => 'View', 'menu_url'  => 'massbody-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>214,'parent_id' => 210,'action'=> 210,'menu_name'  => 'Delete', 'menu_url'  => 'massbody-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>215,'parent_id' => 210,'action'=> 210,'menu_name'  => 'Approve', 'menu_url'  => 'massbody-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>220,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Forming Section', 'menu_url'  => 'forming-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>221,'parent_id' => 220,'action'=> 220,'menu_name'  => 'Store', 'menu_url'  => 'forming-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>222,'parent_id' => 220,'action'=> 220,'menu_name'  => 'Edit', 'menu_url'  => 'forming-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>223,'parent_id' => 220,'action'=> 220,'menu_name'  => 'View', 'menu_url'  => 'forming-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>224,'parent_id' => 220,'action'=> 220,'menu_name'  => 'Delete', 'menu_url'  => 'forming-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>225,'parent_id' => 220,'action'=> 220,'menu_name'  => 'Approve', 'menu_url'  => 'forming-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),


                array('id'=>230,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Shapping Section', 'menu_url'  => 'shapping-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>231,'parent_id' => 230,'action'=> 230,'menu_name'  => 'Store', 'menu_url'  => 'shapping-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>232,'parent_id' => 230,'action'=> 230,'menu_name'  => 'Edit', 'menu_url'  => 'shapping-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>233,'parent_id' => 230,'action'=> 230,'menu_name'  => 'View', 'menu_url'  => 'shapping-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>234,'parent_id' => 230,'action'=> 230,'menu_name'  => 'Delete', 'menu_url'  => 'shapping-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>235,'parent_id' => 230,'action'=> 230,'menu_name'  => 'Approve', 'menu_url'  => 'shapping-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>240,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Dryer Section', 'menu_url'  => 'dryer-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>241,'parent_id' => 240,'action'=> 240,'menu_name'  => 'Store', 'menu_url'  => 'dryer-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>242,'parent_id' => 240,'action'=> 240,'menu_name'  => 'Edit', 'menu_url'  => 'dryer-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>243,'parent_id' => 240,'action'=> 240,'menu_name'  => 'View', 'menu_url'  => 'dryer-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>244,'parent_id' => 240,'action'=> 240,'menu_name'  => 'Delete', 'menu_url'  => 'dryer-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>245,'parent_id' => 240,'action'=> 240,'menu_name'  => 'Approve', 'menu_url'  => 'dryer-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>250,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Glaze Section', 'menu_url'  => 'glaze-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>251,'parent_id' => 250,'action'=> 250,'menu_name'  => 'Store', 'menu_url'  => 'glaze-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>252,'parent_id' => 250,'action'=> 250,'menu_name'  => 'Edit', 'menu_url'  => 'glaze-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>253,'parent_id' => 250,'action'=> 250,'menu_name'  => 'View', 'menu_url'  => 'glaze-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>254,'parent_id' => 250,'action'=> 250,'menu_name'  => 'Delete', 'menu_url'  => 'glaze-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>255,'parent_id' => 250,'action'=> 250,'menu_name'  => 'Approve', 'menu_url'  => 'glaze-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>260,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Kiln Section', 'menu_url'  => 'kiln-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>261,'parent_id' => 260,'action'=> 260,'menu_name'  => 'Store', 'menu_url'  => 'kiln-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>262,'parent_id' => 260,'action'=> 260,'menu_name'  => 'Edit', 'menu_url'  => 'kiln-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>263,'parent_id' => 260,'action'=> 260,'menu_name'  => 'View', 'menu_url'  => 'kiln-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>264,'parent_id' => 260,'action'=> 260,'menu_name'  => 'Delete', 'menu_url'  => 'kiln-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>265,'parent_id' => 260,'action'=> 260,'menu_name'  => 'Approve', 'menu_url'  => 'kiln-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>270,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'H/V Testing Section', 'menu_url'  => 'testing-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>271,'parent_id' => 270,'action'=> 270,'menu_name'  => 'Store', 'menu_url'  => 'testing-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>272,'parent_id' => 270,'action'=> 270,'menu_name'  => 'Edit', 'menu_url'  => 'testing-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>273,'parent_id' => 270,'action'=> 270,'menu_name'  => 'View', 'menu_url'  => 'testing-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>274,'parent_id' => 270,'action'=> 270,'menu_name'  => 'Delete', 'menu_url'  => 'testing-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>275,'parent_id' => 270,'action'=> 270,'menu_name'  => 'Approve', 'menu_url'  => 'testing-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

/*
                array('id'=>280,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Semi Finished Stock', 'menu_url'  => 'semi-finished-stock.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
*/

                array('id'=>290,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Finished Stock Section', 'menu_url'  => 'finished-stock-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>291,'parent_id' => 290,'action'=> 290,'menu_name'  => 'Store', 'menu_url'  => 'finished-stock-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>292,'parent_id' => 290,'action'=> 290,'menu_name'  => 'Edit', 'menu_url'  => 'finished-stock-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>293,'parent_id' => 290,'action'=> 290,'menu_name'  => 'View', 'menu_url'  => 'finished-stock-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>294,'parent_id' => 290,'action'=> 290,'menu_name'  => 'Delete', 'menu_url'  => 'finished-stock-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>295,'parent_id' => 290,'action'=> 290,'menu_name'  => 'Approve', 'menu_url'  => 'finished-stock-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>300,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Assembling Section', 'menu_url'  => 'assembling-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>301,'parent_id' => 300,'action'=> 300,'menu_name'  => 'Store', 'menu_url'  => 'assembling-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>302,'parent_id' => 300,'action'=> 300,'menu_name'  => 'Edit', 'menu_url'  => 'assembling-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>303,'parent_id' => 300,'action'=> 300,'menu_name'  => 'View', 'menu_url'  => 'assembling-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>304,'parent_id' => 300,'action'=> 300,'menu_name'  => 'Delete', 'menu_url'  => 'assembling-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>305,'parent_id' => 300,'action'=> 300,'menu_name'  => 'Approve', 'menu_url'  => 'assembling-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>310,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Packing Section', 'menu_url'  => 'packing-section.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>311,'parent_id' => 310,'action'=> 310,'menu_name'  => 'Store', 'menu_url'  => 'packing-section.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>312,'parent_id' => 310,'action'=> 310,'menu_name'  => 'Edit', 'menu_url'  => 'packing-section.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>313,'parent_id' => 310,'action'=> 310,'menu_name'  => 'View', 'menu_url'  => 'packing-section.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>314,'parent_id' => 310,'action'=> 310,'menu_name'  => 'Delete', 'menu_url'  => 'packing-section.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>315,'parent_id' => 310,'action'=> 310,'menu_name'  => 'Approve', 'menu_url'  => 'packing-section.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>320,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Indirect Costs', 'menu_url'  => 'indirect-costs.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>321,'parent_id' => 320,'action'=> 320,'menu_name'  => 'Store', 'menu_url'  => 'indirect-costs.store', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>322,'parent_id' => 320,'action'=> 320,'menu_name'  => 'Edit', 'menu_url'  => 'indirect-costs.edit', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>323,'parent_id' => 320,'action'=> 320,'menu_name'  => 'View', 'menu_url'  => 'indirect-costs.show', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>324,'parent_id' => 320,'action'=> 320,'menu_name'  => 'Delete', 'menu_url'  => 'indirect-costs.destroy', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>325,'parent_id' => 320,'action'=> 320,'menu_name'  => 'Approve', 'menu_url'  => 'indirect-costs.approve','module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>350,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Production Reports', 'menu_url'  => NULL, 'module_id'  => '2','created_at' => $time,'status' => 1),

                array('id'=>351,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Massbody Report', 'menu_url'  => 'massbody-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>352,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Forming Report', 'menu_url'  => 'forming-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>353,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Shapping Report', 'menu_url'  => 'shapping-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>354,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Dryer Report', 'menu_url'  => 'dryer-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>355,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Glaze Report', 'menu_url'  => 'glaze-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>356,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Kiln Report', 'menu_url'  => 'kiln-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>357,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'HV Report', 'menu_url'  => 'testing-hv-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
/*
                array('id'=>358,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Semi Finished Report', 'menu_url'  => 'semifinished-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
*/
                
                array('id'=>359,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Finished Stock Report', 'menu_url'  => 'finished-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>360,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Assembling Report', 'menu_url'  => 'assembling-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>361,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Monthly Cementing Report', 'menu_url'  => 'cementing-report.index', 'module_id'  => '2','created_at' => $time,'status' => 1),
                array('id'=>362,'parent_id' => 350,'action'=> NULL,'menu_name'  => 'Cost of Production', 'menu_url'  => 'cost-of-production.index', 'module_id'  => '2','created_at' => $time,'status' => 1),





                //Purchase Section
                array('id'=>500,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Setup', 'menu_url'  => NULL, 'module_id'  => '3','created_at' => $time,'status' => 1),

                array('id'=>510,'parent_id' => 500,'action'=> NULL,'menu_name'  => 'Supplier', 'menu_url'  => 'supplier-entry.index', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>511,'parent_id' => 510,'action'=> 510,'menu_name'  => 'Store', 'menu_url'  => 'supplier-entry.store', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>512,'parent_id' => 510,'action'=> 510,'menu_name'  => 'Edit', 'menu_url'  => 'supplier-entry.edit', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>513,'parent_id' => 510,'action'=> 510,'menu_name'  => 'View', 'menu_url'  => 'supplier-entry.show', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>514,'parent_id' => 510,'action'=> 510,'menu_name'  => 'Delete', 'menu_url'  => 'supplier-entry.destroy', 'module_id'  => '3','created_at' => $time,'status' => 1),

                array('id'=>520,'parent_id' => 500,'action'=> NULL,'menu_name'  => 'Cost Type', 'menu_url'  => 'cost-type.index', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>521,'parent_id' => 520,'action'=> 520,'menu_name'  => 'Store', 'menu_url'  => 'cost-type.store', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>522,'parent_id' => 520,'action'=> 520,'menu_name'  => 'Edit', 'menu_url'  => 'cost-type.edit', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>523,'parent_id' => 520,'action'=> 520,'menu_name'  => 'View', 'menu_url'  => 'cost-type.show', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>524,'parent_id' => 520,'action'=> 520,'menu_name'  => 'Delete', 'menu_url'  => 'cost-type.destroy', 'module_id'  => '3','created_at' => $time,'status' => 1),

                array('id'=>540,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Purchase Requisition', 'menu_url'  => 'pr-requisition.index', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>541,'parent_id' => 540,'action'=> 540,'menu_name'  => 'Store', 'menu_url'  => 'pr-requisition.store', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>542,'parent_id' => 540,'action'=> 540,'menu_name'  => 'Edit', 'menu_url'  => 'pr-requisition.edit', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>543,'parent_id' => 540,'action'=> 540,'menu_name'  => 'View', 'menu_url'  => 'pr-requisition.show', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>544,'parent_id' => 540,'action'=> 540,'menu_name'  => 'Delete', 'menu_url'  => 'pr-requisition.destroy', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>545,'parent_id' => 540,'action'=> 540,'menu_name'  => 'Approve', 'menu_url'  => 'pr-requisition.approve', 'module_id'  => '3','created_at' => $time,'status' => 1),

                array('id'=>550,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'PO Receive & Stock Entry', 'menu_url'  => 'po-receive.index', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>551,'parent_id' => 550,'action'=> 550,'menu_name'  => 'Store', 'menu_url'  => 'po-receive.store', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>552,'parent_id' => 550,'action'=> 550,'menu_name'  => 'Edit', 'menu_url'  => 'po-receive.edit', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>553,'parent_id' => 550,'action'=> 550,'menu_name'  => 'View', 'menu_url'  => 'po-receive.show', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>554,'parent_id' => 550,'action'=> 550,'menu_name'  => 'Delete', 'menu_url'  => 'po-receive.destroy', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>555,'parent_id' => 550,'action'=> 550,'menu_name'  => 'Approve', 'menu_url'  => 'po-receive.approve', 'module_id'  => '3','created_at' => $time,'status' => 1),

                array('id'=>560,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Purchase Related Cost Entry', 'menu_url'  => 'po-cost-entry.index', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>561,'parent_id' => 560,'action'=> 560,'menu_name'  => 'Store', 'menu_url'  => 'po-cost-entry.store', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>562,'parent_id' => 560,'action'=> 560,'menu_name'  => 'Edit', 'menu_url'  => 'po-cost-entry.edit', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>563,'parent_id' => 560,'action'=> 560,'menu_name'  => 'View', 'menu_url'  => 'po-cost-entry.show', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>564,'parent_id' => 560,'action'=> 560,'menu_name'  => 'Delete', 'menu_url'  => 'po-cost-entry.destroy', 'module_id'  => '3','created_at' => $time,'status' => 1),

                array('id'=>570,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Purchase Return', 'menu_url'  => 'po-return.index', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>571,'parent_id' => 570,'action'=> 570,'menu_name'  => 'Store', 'menu_url'  => 'po-return.store', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>572,'parent_id' => 570,'action'=> 570,'menu_name'  => 'Edit', 'menu_url'  => 'po-return.edit', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>573,'parent_id' => 570,'action'=> 570,'menu_name'  => 'View', 'menu_url'  => 'po-return.show', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>574,'parent_id' => 570,'action'=> 570,'menu_name'  => 'Delete', 'menu_url'  => 'po-return.destroy', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>575,'parent_id' => 570,'action'=> 570,'menu_name'  => 'Approve', 'menu_url'  => 'po-return.approve', 'module_id'  => '3','created_at' => $time,'status' => 1),

                //Report
                array('id'=>600,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Report', 'menu_url'  => NULL, 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>601,'parent_id' => 600,'action'=> NULL,'menu_name'  => 'Purchase Report', 'menu_url'  => 'purchase-report.purchase', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>602,'parent_id' => 600,'action'=> NULL,'menu_name'  => 'Purchase Return Report', 'menu_url'  => 'purchase-report.return_purchase', 'module_id'  => '3','created_at' => $time,'status' => 1),
                array('id'=>603,'parent_id' => 600,'action'=> NULL,'menu_name'  => 'Supplier Summary Report', 'menu_url'  => 'purchase-report.purchase_summary', 'module_id'  => '3','created_at' => $time,'status' => 1),





                //Sales Section
                array('id'=>700,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Setup', 'menu_url'  => NULL, 'module_id'  => '4','created_at' => $time,'status' => 1),

                array('id'=>710,'parent_id' => 700,'action'=> NULL,'menu_name'  => 'Customer', 'menu_url'  => 'customer.index', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>711,'parent_id' => 710,'action'=> 710,'menu_name'  => 'Store', 'menu_url'  => 'customer.store', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>712,'parent_id' => 710,'action'=> 710,'menu_name'  => 'Edit', 'menu_url'  => 'customer.edit', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>713,'parent_id' => 710,'action'=> 710,'menu_name'  => 'View', 'menu_url'  => 'customer.show', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>714,'parent_id' => 710,'action'=> 710,'menu_name'  => 'Delete', 'menu_url'  => 'customer.destroy', 'module_id'  => '4','created_at' => $time,'status' => 1),

                //Sales / Tender Invoice
                array('id'=>720,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Sales / Tender Requisition', 'menu_url'  => 'sales-invoice.index', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>721,'parent_id' => 720,'action'=> 720,'menu_name'  => 'Store', 'menu_url'  => 'sales-invoice.store', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>723,'parent_id' => 720,'action'=> 720,'menu_name'  => 'Edit', 'menu_url'  => 'sales-invoice.edit', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>724,'parent_id' => 720,'action'=> 720,'menu_name'  => 'View', 'menu_url'  => 'sales-invoice.show', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>725,'parent_id' => 720,'action'=> 720,'menu_name'  => 'Delete', 'menu_url'  => 'sales-invoice.destroy', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>726,'parent_id' => 720,'action'=> 720,'menu_name'  => 'Approve', 'menu_url'  => 'sales-invoice.approve', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>727,'parent_id' => 720,'action'=> 720,'menu_name'  => 'Without Price', 'menu_url'  => 'sales-invoice.show-without-price', 'module_id'  => '4','created_at' => $time,'status' => 1),

                array('id'=>730,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Delivery Challan', 'menu_url'  => 'sales-delivery-challan.index', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>731,'parent_id' => 730,'action'=> 730,'menu_name'  => 'Store', 'menu_url'  => 'sales-delivery-challan.store', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>732,'parent_id' => 730,'action'=> 730,'menu_name'  => 'Edit', 'menu_url'  => 'sales-delivery-challan.edit', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>733,'parent_id' => 730,'action'=> 730,'menu_name'  => 'View', 'menu_url'  => 'sales-delivery-challan.show', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>734,'parent_id' => 730,'action'=> 730,'menu_name'  => 'Delete', 'menu_url'  => 'sales-delivery-challan.destroy', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>735,'parent_id' => 730,'action'=> 730,'menu_name'  => 'Approve', 'menu_url'  => 'sales-delivery-challan.approve', 'module_id'  => '4','created_at' => $time,'status' => 1),

                array('id'=>740,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Delivery Return', 'menu_url'  => 'sales-delivery-return.index', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>741,'parent_id' => 740,'action'=> 740,'menu_name'  => 'Store', 'menu_url'  => 'sales-delivery-return.store', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>742,'parent_id' => 740,'action'=> 740,'menu_name'  => 'Edit', 'menu_url'  => 'sales-delivery-return.edit', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>743,'parent_id' => 740,'action'=> 740,'menu_name'  => 'View', 'menu_url'  => 'sales-delivery-return.show', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>744,'parent_id' => 740,'action'=> 740,'menu_name'  => 'Delete', 'menu_url'  => 'sales-delivery-return.destroy', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>745,'parent_id' => 740,'action'=> 740,'menu_name'  => 'Approve', 'menu_url'  => 'sales-delivery-return.approve', 'module_id'  => '4','created_at' => $time,'status' => 1),

                ///Report
                array('id'=>800,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Report', 'menu_url'  => NULL, 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>801,'parent_id' => 800,'action'=> NULL,'menu_name'  => 'Sales Report', 'menu_url'  => 'sales-report.sales', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>802,'parent_id' => 800,'action'=> NULL,'menu_name'  => 'Sales Return Report', 'menu_url'  => 'sales-report.returnd', 'module_id'  => '4','created_at' => $time,'status' => 1),
                array('id'=>803,'parent_id' => 800,'action'=> NULL,'menu_name'  => 'Customer Summary Report', 'menu_url'  => 'sales-report.sales_summary', 'module_id'  => '4','created_at' => $time,'status' => 1),



                // Inventory Section
                array('id' => 890,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Setup', 'menu_url'  => NULL, 'module_id'  => '5','created_at' => $time,'status' => 1),

                array('id'=>900,'parent_id' => 890,'action'=> NULL,'menu_name'  => 'Return Type', 'menu_url'  => 'return-type.create', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>901,'parent_id' => 900,'action'=> 900,'menu_name'  => 'Store', 'menu_url'  => 'return-type.store', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>902,'parent_id' => 900,'action'=> 900,'menu_name'  => 'Edit', 'menu_url'  => 'return-type.edit', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>903,'parent_id' => 900,'action'=> 900,'menu_name'  => 'View', 'menu_url'  => 'return-type.show', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>904,'parent_id' => 900,'action'=> 900,'menu_name'  => 'Delete', 'menu_url'  => 'return-type.destroy', 'module_id'  => '5','created_at' => $time,'status' => 1),

                array('id'=>910,'parent_id' => 890,'action'=> NULL,'menu_name'  => 'Warehouse', 'menu_url'  => 'ware-house.create', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>911,'parent_id' => 910,'action'=> 910,'menu_name'  => 'Store', 'menu_url'  => 'ware-house.store', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>912,'parent_id' => 910,'action'=> 910,'menu_name'  => 'Edit', 'menu_url'  => 'ware-house.edit', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>913,'parent_id' => 910,'action'=> 910,'menu_name'  => 'View', 'menu_url'  => 'ware-house.show', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>914,'parent_id' => 910,'action'=> 910,'menu_name'  => 'Delete', 'menu_url'  => 'ware-house.destroy', 'module_id'  => '5','created_at' => $time,'status' => 1),

                array('id'=>930,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Opening Stock', 'menu_url'  => 'stock-open.index','module_id'  => '5','created_at' => $time,'status' => 1),

                array('id'=>940,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Stock Transfer', 'menu_url'  => 'stock-transfer.index', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>941,'parent_id' => 940,'action'=>940,'menu_name'  => 'Store', 'menu_url'  => 'stock-transfer.store','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>942,'parent_id' => 940,'action'=>940,'menu_name'  => 'Edit', 'menu_url'  => 'stock-transfer.edit','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>943,'parent_id' => 940,'action'=>940,'menu_name'  => 'View', 'menu_url'  => 'stock-transfer.show','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>944,'parent_id' => 940,'action'=>940,'menu_name'  => 'Delete', 'menu_url'  => 'stock-transfer.destroy','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>945,'parent_id' => 940,'action'=>940,'menu_name'  => 'Approve', 'menu_url'  => 'stock-transfer.approve','module_id'  => '5','created_at' => $time,'status' => 1),



                array('id'=>950,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Stock Adjust', 'menu_url'  => 'stock-adjust.index','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>951,'parent_id' => 950,'action'=>950,'menu_name'  => 'Store', 'menu_url'  => 'stock-adjust.store','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>952,'parent_id' => 950,'action'=>950,'menu_name'  => 'Edit', 'menu_url'  => 'stock-adjust.edit','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>953,'parent_id' => 950,'action'=>950,'menu_name'  => 'View', 'menu_url'  => 'stock-adjust.show','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>954,'parent_id' => 950,'action'=>950,'menu_name'  => 'Delete', 'menu_url'  => 'stock-adjust.destroy','module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>955,'parent_id' => 950,'action'=>950,'menu_name'  => 'Approve', 'menu_url'  => 'stock-adjust.approve','module_id'  => '5','created_at' => $time,'status' => 1),

                array('id'=>960,'parent_id' => 0,'action'=> NULL,'menu_name'  => 'Product Damage', 'menu_url'  => 'product-damage.index', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>961,'parent_id' => 960,'action'=> 960,'menu_name'  => 'Store', 'menu_url'  => 'product-damage.store', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>962,'parent_id' => 960,'action'=> 960,'menu_name'  => 'Edit', 'menu_url'  => 'product-damage.edit', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>963,'parent_id' => 960,'action'=> 960,'menu_name'  => 'View', 'menu_url'  => 'product-damage.show', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>964,'parent_id' => 960,'action'=> 960,'menu_name'  => 'Delete', 'menu_url'  => 'product-damage.destroy', 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>965,'parent_id' => 960,'action'=> 960,'menu_name'  => 'Approve', 'menu_url'  => 'product-damage.approve', 'module_id'  => '5','created_at' => $time,'status' => 1),

                array('id'=>1000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Report', 'menu_url'  => NULL, 'module_id'  => '5','created_at' => $time,'status' => 1),
                array('id'=>1001,'parent_id' => 1000,'action'=> NULL,'menu_name'  => 'Stock Report', 'menu_url'  => 'stock-report.stocks', 'module_id'  => '5','created_at' => $time,'status' => 1),



                //Accounts Section
                array('id'=>1100,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Setup', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1110,'parent_id' => 1100,'action'=> NULL,'menu_name'  => 'Bank Name', 'menu_url'  => 'bank.create', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1111,'parent_id' => 1110,'action'=> 1110,'menu_name'  => 'Store', 'menu_url'  => 'bank.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1112,'parent_id' => 1110,'action'=> 1110,'menu_name'  => 'Edit', 'menu_url'  => 'bank.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1113,'parent_id' => 1110,'action'=> 1110,'menu_name'  => 'View', 'menu_url'  => 'bank.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1114,'parent_id' => 1110,'action'=> 1110,'menu_name'  => 'Delete', 'menu_url'  => 'bank.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1120,'parent_id' => 1100,'action'=> NULL,'menu_name'  => 'Bank Branch', 'menu_url'  => 'bank-branch.create', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1121,'parent_id' => 1120,'action'=> 1120,'menu_name'  => 'Store', 'menu_url'  => 'bank-branch.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1122,'parent_id' => 1120,'action'=> 1120,'menu_name'  => 'Edit', 'menu_url'  => 'bank-branch.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1123,'parent_id' => 1120,'action'=> 1120,'menu_name'  => 'View', 'menu_url'  => 'bank-branch.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1124,'parent_id' => 1120,'action'=> 1120,'menu_name'  => 'Delete', 'menu_url'  => 'bank-branch.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1130,'parent_id' => 1100,'action'=> NULL,'menu_name'  => 'Bank Account', 'menu_url'  => 'bank-account.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1131,'parent_id' => 1130,'action'=> 1130,'menu_name'  => 'Store', 'menu_url'  => 'bank-account.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1132,'parent_id' => 1130,'action'=> 1130,'menu_name'  => 'Edit', 'menu_url'  => 'bank-account.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1133,'parent_id' => 1130,'action'=> 1130,'menu_name'  => 'View', 'menu_url'  => 'bank-account.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1134,'parent_id' => 1130,'action'=> 1130,'menu_name'  => 'Delete', 'menu_url'  => 'bank-account.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1140,'parent_id' => 1100,'action'=> NULL,'menu_name'  => 'Check Book Leaf', 'menu_url'  => 'check-book-leaf.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1141,'parent_id' => 1140,'action'=> 1140,'menu_name'  => 'Store', 'menu_url'  => 'check-book-leaf.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1142,'parent_id' => 1140,'action'=> 1140,'menu_name'  => 'Edit', 'menu_url'  => 'check-book-leaf.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1143,'parent_id' => 1140,'action'=> 1140,'menu_name'  => 'View', 'menu_url'  => 'check-book-leaf.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1144,'parent_id' => 1140,'action'=> 1140,'menu_name'  => 'Delete', 'menu_url'  => 'check-book-leaf.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1145,'parent_id' => 1140,'action'=> 1140,'menu_name'  => 'Approve', 'menu_url'  => 'check-book-leaf.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1150,'parent_id' => 1100,'action'=> NULL,'menu_name'  => 'Project Info', 'menu_url'  => 'project-info.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1151,'parent_id' => 1150,'action'=> 1150,'menu_name'  => 'Store', 'menu_url'  => 'project-info.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1152,'parent_id' => 1150,'action'=> 1150,'menu_name'  => 'Edit', 'menu_url'  => 'project-info.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1153,'parent_id' => 1150,'action'=> 1150,'menu_name'  => 'View', 'menu_url'  => 'project-info.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1154,'parent_id' => 1150,'action'=> 1150,'menu_name'  => 'Delete', 'menu_url'  => 'project-info.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),


                /*** chart of Accounce ***/
                array('id'=>1200,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Chart Of Accounts', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1210,'parent_id' => 1200,'action'=> NULL,'menu_name'  => 'Accounts Main Code', 'menu_url'  => 'account-main-code.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1211,'parent_id' => 1210,'action'=> 1210,'menu_name'  => 'Store', 'menu_url'  => 'account-main-code.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1212,'parent_id' => 1210,'action'=> 1210,'menu_name'  => 'Edit', 'menu_url'  => 'account-main-code.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1213,'parent_id' => 1210,'action'=> 1210,'menu_name'  => 'View', 'menu_url'  => 'account-main-code.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1214,'parent_id' => 1210,'action'=> 1210,'menu_name'  => 'Delete', 'menu_url'  => 'account-main-code.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1220,'parent_id' => 1200,'action'=> NULL,'menu_name'  => 'Accounts Sub Code', 'menu_url'  => 'account-sub-code.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1221,'parent_id' => 1220,'action'=> 1220,'menu_name'  => 'Store', 'menu_url'  => 'account-sub-code.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1222,'parent_id' => 1220,'action'=> 1220,'menu_name'  => 'Edit', 'menu_url'  => 'account-sub-code.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1223,'parent_id' => 1220,'action'=> 1220,'menu_name'  => 'View', 'menu_url'  => 'account-sub-code.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1224,'parent_id' => 1220,'action'=> 1220,'menu_name'  => 'Delete', 'menu_url'  => 'account-sub-code.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1230,'parent_id' => 1200,'action'=> NULL,'menu_name'  => 'Accounts Sub Code2', 'menu_url'  => 'account-sub-code2.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1231,'parent_id' => 1230,'action'=> 1230,'menu_name'  => 'Store', 'menu_url'  => 'account-sub-code2.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1232,'parent_id' => 1230,'action'=> 1230,'menu_name'  => 'Edit', 'menu_url'  => 'account-sub-code2.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1233,'parent_id' => 1230,'action'=> 1230,'menu_name'  => 'View', 'menu_url'  => 'account-sub-code2.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1234,'parent_id' => 1230,'action'=> 1230,'menu_name'  => 'Delete', 'menu_url'  => 'account-sub-code2.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1240,'parent_id' => 1200,'action'=> NULL,'menu_name'  => 'Chart Of Accounts', 'menu_url'  => 'chart-of-accounts.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1241,'parent_id' => 1240,'action'=> 1240,'menu_name'  => 'Store', 'menu_url'  => 'chart-of-accounts.store', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1242,'parent_id' => 1240,'action'=> 1240,'menu_name'  => 'Edit', 'menu_url'  => 'chart-of-accounts.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1243,'parent_id' => 1240,'action'=> 1240,'menu_name'  => 'View', 'menu_url'  => 'chart-of-accounts.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1244,'parent_id' => 1240,'action'=> 1240,'menu_name'  => 'Delete', 'menu_url'  => 'chart-of-accounts.destroy', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1250,'parent_id' => 1200,'action'=>NULL,'menu_name'  => 'Opening Balance', 'menu_url'  => 'opening-balance.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1251,'parent_id' => 1250,'action'=>1250,'menu_name'  => 'Store', 'menu_url'  => 'opening-balance.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1252,'parent_id' => 1250,'action'=>1250,'menu_name'  => 'Edit', 'menu_url'  => 'opening-balance.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1253,'parent_id' => 1250,'action'=>1250,'menu_name'  => 'View', 'menu_url'  => 'opening-balance.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1254,'parent_id' => 1250,'action'=>1250,'menu_name'  => 'Delete', 'menu_url'  => 'opening-balance.destroy','module_id'  => '6','created_at' => $time,'status' => 1),

                /***** Accounce  section***************/

                /*Production Section*/
                array('id'=>1300,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Production Voucher', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1310,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Requisition Voucher', 'menu_url'  => 'acc-req-rm-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1311,'parent_id' => 1310,'action'=>1310,'menu_name'  => 'Edit', 'menu_url'  => 'acc-req-rm-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1312,'parent_id' => 1310,'action'=>1310,'menu_name'  => 'View', 'menu_url'  => 'acc-req-rm-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1313,'parent_id' => 1310,'action'=>1310,'menu_name'  => 'Approve', 'menu_url'  => 'acc-req-rm-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1320,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Massbody Voucher', 'menu_url'  => 'acc-mass-body-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1321,'parent_id' => 1320,'action'=>1320,'menu_name'  => 'Edit', 'menu_url'  => 'acc-mass-body-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1322,'parent_id' => 1320,'action'=>1320,'menu_name'  => 'View', 'menu_url'  => 'acc-mass-body-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1323,'parent_id' => 1320,'action'=>1320,'menu_name'  => 'Approve', 'menu_url'  => 'acc-mass-body-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1330,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Forming Voucher', 'menu_url'  => 'acc-forming-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1331,'parent_id' => 1330,'action'=>1330,'menu_name'  => 'Edit', 'menu_url'  => 'acc-forming-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1332,'parent_id' => 1330,'action'=>1330,'menu_name'  => 'View', 'menu_url'  => 'acc-forming-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1333,'parent_id' => 1330,'action'=>1330,'menu_name'  => 'Approve', 'menu_url'  => 'acc-forming-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1340,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Shapping Voucher', 'menu_url'  => 'acc-shapping-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1341,'parent_id' => 1340,'action'=>1340,'menu_name'  => 'Edit', 'menu_url'  => 'acc-shapping-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1342,'parent_id' => 1340,'action'=>1340,'menu_name'  => 'View', 'menu_url'  => 'acc-shapping-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1343,'parent_id' => 1340,'action'=>1340,'menu_name'  => 'Approve', 'menu_url'  => 'acc-shapping-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1350,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Dryer Voucher', 'menu_url'  => 'acc-dryer-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1351,'parent_id' => 1350,'action'=>1350,'menu_name'  => 'Edit', 'menu_url'  => 'acc-dryer-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1352,'parent_id' => 1350,'action'=>1350,'menu_name'  => 'View', 'menu_url'  => 'acc-dryer-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1353,'parent_id' => 1350,'action'=>1350,'menu_name'  => 'Approve', 'menu_url'  => 'acc-dryer-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1360,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Glaze Voucher', 'menu_url'  => 'acc-glaze-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1361,'parent_id' => 1360,'action'=>1360,'menu_name'  => 'Edit', 'menu_url'  => 'acc-glaze-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1362,'parent_id' => 1360,'action'=>1360,'menu_name'  => 'View', 'menu_url'  => 'acc-glaze-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1363,'parent_id' => 1360,'action'=>1360,'menu_name'  => 'Approve', 'menu_url'  => 'acc-glaze-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1370,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Kiln Voucher', 'menu_url'  => 'acc-kiln-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1371,'parent_id' => 1370,'action'=>1370,'menu_name'  => 'Edit', 'menu_url'  => 'acc-kiln-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1372,'parent_id' => 1370,'action'=>1370,'menu_name'  => 'View', 'menu_url'  => 'acc-kiln-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1373,'parent_id' => 1370,'action'=>1370,'menu_name'  => 'Approve', 'menu_url'  => 'acc-kiln-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1380,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Testing/HV Voucher', 'menu_url'  => 'acc-testing-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1381,'parent_id' => 1380,'action'=>1380,'menu_name'  => 'Edit', 'menu_url'  => 'acc-testing-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1382,'parent_id' => 1380,'action'=>1380,'menu_name'  => 'View', 'menu_url'  => 'acc-testing-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1383,'parent_id' => 1380,'action'=>1380,'menu_name'  => 'Approve', 'menu_url'  => 'acc-testing-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1390,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Finished Stock Voucher', 'menu_url'  => 'acc-finish-stock-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1391,'parent_id' => 1390,'action'=>1390,'menu_name'  => 'Edit', 'menu_url'  => 'acc-finish-stock-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1392,'parent_id' => 1390,'action'=>1390,'menu_name'  => 'View', 'menu_url'  => 'acc-finish-stock-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1393,'parent_id' => 1390,'action'=>1390,'menu_name'  => 'Approve', 'menu_url'  => 'acc-finish-stock-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1400,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Assembling Voucher', 'menu_url'  => 'acc-assembly-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1401,'parent_id' => 1400,'action'=>1400,'menu_name'  => 'Edit', 'menu_url'  => 'acc-assembly-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1402,'parent_id' => 1400,'action'=>1400,'menu_name'  => 'View', 'menu_url'  => 'acc-assembly-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1403,'parent_id' => 1400,'action'=>1400,'menu_name'  => 'Approve', 'menu_url'  => 'acc-assembly-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'  => 1410,'parent_id' => 1300,'action'=>NULL,'menu_name'  => 'Packing Voucher', 'menu_url'  => 'acc-packing-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'  => 1411,'parent_id' => 1410,'action'=>1410,'menu_name'  => 'Edit', 'menu_url'  => 'acc-packing-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'  => 1412,'parent_id' => 1410,'action'=>1410,'menu_name'  => 'View', 'menu_url'  => 'acc-packing-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'  => 1413,'parent_id' => 1410,'action'=>1410,'menu_name'  => 'Approve', 'menu_url'  => 'acc-packing-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),



                /*Purchase voucehr*/

                array('id'=>1500,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Purchase Voucher', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1510,'parent_id' => 1500,'action'=>NULL,'menu_name'  => 'Purchase Invoice Voucher', 'menu_url'  => 'purchase-invoice-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1511,'parent_id' => 1510,'action'=>1510,'menu_name'  => 'Store', 'menu_url'  => 'purchase-invoice-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1512,'parent_id' => 1510,'action'=>1510,'menu_name'  => 'Edit', 'menu_url'  => 'purchase-invoice-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1513,'parent_id' => 1510,'action'=>1510,'menu_name'  => 'View', 'menu_url'  => 'purchase-invoice-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1514,'parent_id' => 1510,'action'=>1510,'menu_name'  => 'Delete', 'menu_url'  => 'purchase-invoice-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1515,'parent_id' => 1510,'action'=>1510,'menu_name'  => 'Approve', 'menu_url'  => 'purchase-invoice-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1520,'parent_id' => 1500,'action'=>NULL,'menu_name'  => 'Purchase Return Voucher', 'menu_url'  => 'purchase-return-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1521,'parent_id' => 1520,'action'=>1520,'menu_name'  => 'Store', 'menu_url'  => 'purchase-return-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1522,'parent_id' => 1520,'action'=>1520,'menu_name'  => 'Edit', 'menu_url'  => 'purchase-return-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1523,'parent_id' => 1520,'action'=>1520,'menu_name'  => 'View', 'menu_url'  => 'purchase-return-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1524,'parent_id' => 1520,'action'=>1520,'menu_name'  => 'Delete', 'menu_url'  => 'purchase-return-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1525,'parent_id' => 1520,'action'=>1520,'menu_name'  => 'Approve', 'menu_url'  => 'purchase-return-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),


                /*Sales Voucher*/

                array('id'=>1550,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Sales Voucher', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1560,'parent_id' => 1550,'action'=>NULL,'menu_name'  => 'Sales Invoice Voucher', 'menu_url'  => 'sales-invoice-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1561,'parent_id' => 1560,'action'=>1560,'menu_name'  => 'Store', 'menu_url'  => 'sales-invoice-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1562,'parent_id' => 1560,'action'=>1560,'menu_name'  => 'Edit', 'menu_url'  => 'sales-invoice-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1563,'parent_id' => 1560,'action'=>1560,'menu_name'  => 'View', 'menu_url'  => 'sales-invoice-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1564,'parent_id' => 1560,'action'=>1560,'menu_name'  => 'Delete', 'menu_url'  => 'sales-invoice-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1565,'parent_id' => 1560,'action'=>1560,'menu_name'  => 'Approve', 'menu_url'  => 'sales-invoice-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),


                array('id'=>1570,'parent_id' => 1550,'action'=>NULL,'menu_name'  => 'Sales Return Voucher', 'menu_url'  => 'sales-return-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1571,'parent_id' => 1570,'action'=>1570,'menu_name'  => 'Store', 'menu_url'  => 'sales-return-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1572,'parent_id' => 1570,'action'=>1570,'menu_name'  => 'Edit', 'menu_url'  => 'sales-return-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1573,'parent_id' => 1570,'action'=>1570,'menu_name'  => 'View', 'menu_url'  => 'sales-return-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1574,'parent_id' => 1570,'action'=>1570,'menu_name'  => 'Delete', 'menu_url'  => 'sales-return-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1575,'parent_id' => 1570,'action'=>1570,'menu_name'  => 'Approve', 'menu_url'  => 'sales-return-voucher.approve','module_id'  => '6','created_at' => $time,'status' => 1),



                /*Lc Section*/
                array('id'=>1700,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC Voucher', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1710,'parent_id' => 1700,'action'=>NULL,'menu_name'  => 'LC Opening Section', 'menu_url'  => 'acc-lc-opening-section.index','module_id'  => '6','created_at' => $time,'status' => 0),
                array('id'=>1711,'parent_id' => 1710,'action'=>1710,'menu_name'  => 'Edit', 'menu_url'  => 'acc-lc-opening-section.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1712,'parent_id' => 1710,'action'=>1710,'menu_name'  => 'View', 'menu_url'  => 'acc-lc-opening-section.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1713,'parent_id' => 1710,'action'=>1710,'menu_name'  => 'Approve', 'menu_url'  => 'acc-lc-opening-section.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1720,'parent_id' => 1700,'action'=>NULL,'menu_name'  => 'LC Insurance', 'menu_url'  => 'acc-lc-insurance.index', 'module_id'  => '6','created_at' => $time,'status' => 0),
                array('id'=>1721,'parent_id' => 1720,'action'=> 1720,'menu_name'  => 'Edit', 'menu_url'  => 'acc-lc-insurance.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1722,'parent_id' => 1720,'action'=> 1720,'menu_name'  => 'View', 'menu_url'  => 'acc-lc-insurance.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1723,'parent_id' => 1720,'action'=> 1720,'menu_name'  => 'Approve', 'menu_url'  => 'acc-lc-insurance.approve', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1730,'parent_id' => 1700,'action'=>NULL,'menu_name'  => 'LC Margin Entry', 'menu_url'  => 'acc-lc-cf-value-margin-entry.index', 'module_id'  => '6','created_at' => $time,'status' => 0),
                array('id'=>1731,'parent_id' => 1730,'action'=> 1730,'menu_name'  => 'Edit', 'menu_url'  => 'acc-lc-cf-value-margin-entry.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1732,'parent_id' => 1730,'action'=> 1730,'menu_name'  => 'View', 'menu_url'  => 'acc-lc-cf-value-margin-entry.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1734,'parent_id' => 1730,'action'=> 1730,'menu_name'  => 'Approve', 'menu_url'  => 'acc-lc-cf-value-margin-entry.approve', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1740,'parent_id' => 1700,'action'=>NULL,'menu_name'  => 'LATR Entry', 'menu_url'  => 'acc-latr-entry.index', 'module_id'  => '6','created_at' => $time,'status' => 0),
                array('id'=>1741,'parent_id' => 1740,'action'=> 1740,'menu_name'  => 'Edit', 'menu_url'  => 'acc-latr-entry.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1742,'parent_id' => 1740,'action'=> 1740,'menu_name'  => 'View', 'menu_url'  => 'acc-latr-entry.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1743,'parent_id' => 1740,'action'=> 1740,'menu_name'  => 'Approve', 'menu_url'  => 'acc-latr-entry.approve', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1750,'parent_id' => 1700,'action'=>NULL,'menu_name'  => 'Custom Duty Charge Entry', 'menu_url'  => 'acc-custom-duty-charge-entry.index', 'module_id'  => '6','created_at' => $time,'status' => 0),
                array('id'=>1751,'parent_id' => 1750,'action'=> 1750,'menu_name'  => 'Edit', 'menu_url'  => 'acc-custom-duty-charge-entry.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1752,'parent_id' => 1750,'action'=> 1750,'menu_name'  => 'View', 'menu_url'  => 'acc-custom-duty-charge-entry.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1753,'parent_id' => 1750,'action'=> 1750,'menu_name'  => 'Approve', 'menu_url'  => 'acc-custom-duty-charge-entry.approve', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1760,'parent_id' => 1700,'action'=>NULL,'menu_name'  => 'Others Charge Entry', 'menu_url'  => 'acc-others-charge-entry.index', 'module_id'  => '6','created_at' => $time,'status' => 0),
                array('id'=>1761,'parent_id' => 1760,'action'=> 1760,'menu_name'  => 'Edit', 'menu_url'  => 'acc-others-charge-entry.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1762,'parent_id' => 1760,'action'=> 1760,'menu_name'  => 'View', 'menu_url'  => 'acc-others-charge-entry.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1763,'parent_id' => 1760,'action'=> 1760,'menu_name'  => 'Approve', 'menu_url'  => 'acc-others-charge-entry.approve', 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1770,'parent_id' => 1700,'action'=>NULL,'menu_name'  => 'LC Stock Entry', 'menu_url'  => 'acc-lc-stock-entry.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1771,'parent_id' => 1770,'action'=> 1770,'menu_name'  => 'Edit', 'menu_url'  => 'acc-lc-stock-entry.edit', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1772,'parent_id' => 1770,'action'=> 1770,'menu_name'  => 'View', 'menu_url'  => 'acc-lc-stock-entry.show', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1773,'parent_id' => 1770,'action'=> 1770,'menu_name'  => 'Approve', 'menu_url'  => 'acc-lc-stock-entry.approve', 'module_id'  => '6','created_at' => $time,'status' => 1),




                /*Accounts voucher*/

                array('id'=>1900,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Voucher', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1910,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Journal Voucher', 'menu_url'  => 'journal-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1911,'parent_id' => 1910,'action'=>1910,'menu_name'  => 'Store', 'menu_url'  => 'journal-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1912,'parent_id' => 1910,'action'=>1910,'menu_name'  => 'Edit', 'menu_url'  => 'journal-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1913,'parent_id' => 1910,'action'=>1910,'menu_name'  => 'View', 'menu_url'  => 'journal-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1914,'parent_id' => 1910,'action'=>1910,'menu_name'  => 'Delete', 'menu_url'  => 'journal-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1915,'parent_id' => 1910,'action'=>1910,'menu_name'  => 'Approve', 'menu_url'  => 'journal-vouchers.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1920,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Contra Entry Voucher', 'menu_url'  => 'contra-entry-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1921,'parent_id' => 1920,'action'=>1920,'menu_name'  => 'Store', 'menu_url'  => 'contra-entry-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1922,'parent_id' => 1920,'action'=>1920,'menu_name'  => 'Edit', 'menu_url'  => 'contra-entry-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1923,'parent_id' => 1920,'action'=>1920,'menu_name'  => 'View', 'menu_url'  => 'contra-entry-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1924,'parent_id' => 1920,'action'=>1920,'menu_name'  => 'Delete', 'menu_url'  => 'contra-entry-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1925,'parent_id' => 1920,'action'=>1920,'menu_name'  => 'Approve', 'menu_url'  => 'contra-entry-vouchers.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1930,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Cash Payment Voucher', 'menu_url'  => 'cash-payment-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1931,'parent_id' => 1930,'action'=>1930,'menu_name'  => 'Store', 'menu_url'  => 'cash-payment-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1932,'parent_id' => 1930,'action'=>1930,'menu_name'  => 'Edit', 'menu_url'  => 'cash-payment-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1933,'parent_id' => 1930,'action'=>1930,'menu_name'  => 'View', 'menu_url'  => 'cash-payment-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1934,'parent_id' => 1930,'action'=>1930,'menu_name'  => 'Delete', 'menu_url'  => 'cash-payment-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1935,'parent_id' => 1930,'action'=>1930,'menu_name'  => 'Approve', 'menu_url'  => 'cash-payment-vouchers.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1940,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Bank Payment Voucher', 'menu_url'  => 'bank-payment-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1941,'parent_id' => 1940,'action'=>1940,'menu_name'  => 'Store', 'menu_url'  => 'bank-payment-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1942,'parent_id' => 1940,'action'=>1940,'menu_name'  => 'Edit', 'menu_url'  => 'bank-payment-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1943,'parent_id' => 1940,'action'=>1940,'menu_name'  => 'View', 'menu_url'  => 'bank-payment-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1944,'parent_id' => 1940,'action'=>1940,'menu_name'  => 'Delete', 'menu_url'  => 'bank-payment-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1945,'parent_id' => 1940,'action'=>1940,'menu_name'  => 'Approve', 'menu_url'  => 'bank-payment-vouchers.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1950,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Cash Receipt Voucher', 'menu_url'  => 'cash-receipt-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1951,'parent_id' => 1950,'action'=>1950,'menu_name'  => 'Store', 'menu_url'  => 'cash-receipt-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1952,'parent_id' => 1950,'action'=>1950,'menu_name'  => 'Edit', 'menu_url'  => 'cash-receipt-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1953,'parent_id' => 1950,'action'=>1950,'menu_name'  => 'View', 'menu_url'  => 'cash-receipt-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1954,'parent_id' => 1950,'action'=>1950,'menu_name'  => 'Delete', 'menu_url'  => 'cash-receipt-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1955,'parent_id' => 1950,'action'=>1950,'menu_name'  => 'Approve', 'menu_url'  => 'cash-receipt-vouchers.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1960,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Bank Receipt Voucher', 'menu_url'  => 'bank-receipt-voucher.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1961,'parent_id' => 1960,'action'=>1960,'menu_name'  => 'Store', 'menu_url'  => 'bank-receipt-voucher.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1962,'parent_id' => 1960,'action'=>1960,'menu_name'  => 'Edit', 'menu_url'  => 'bank-receipt-voucher.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1963,'parent_id' => 1960,'action'=>1960,'menu_name'  => 'View', 'menu_url'  => 'bank-receipt-voucher.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1964,'parent_id' => 1960,'action'=>1960,'menu_name'  => 'Delete', 'menu_url'  => 'bank-receipt-voucher.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1965,'parent_id' => 1960,'action'=>1960,'menu_name'  => 'Approve', 'menu_url'  => 'bank-receipt-vouchers.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1970,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Bank Transfer', 'menu_url'  => 'bank-transfer-deposit.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1971,'parent_id' => 1970,'action'=>1970,'menu_name'  => 'Store', 'menu_url'  => 'bank-transfer-deposit.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1972,'parent_id' => 1970,'action'=>1970,'menu_name'  => 'Edit', 'menu_url'  => 'bank-transfer-deposit.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1973,'parent_id' => 1970,'action'=>1970,'menu_name'  => 'View', 'menu_url'  => 'bank-transfer-deposit.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1974,'parent_id' => 1970,'action'=>1970,'menu_name'  => 'Delete', 'menu_url'  => 'bank-transfer-deposit.destroy','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1975,'parent_id' => 1970,'action'=>1970,'menu_name'  => 'Approve', 'menu_url'  => 'bank-transfer-deposits.approve','module_id'  => '6','created_at' => $time,'status' => 1),

                array('id'=>1980,'parent_id' => 1900,'action'=>NULL,'menu_name'  => 'Fixed Asset Depreciation', 'menu_url'  => 'fixed-asset-depreciation.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1981,'parent_id' => 1980,'action'=>1980,'menu_name'  => 'Store', 'menu_url'  => 'fixed-asset-depreciation.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1982,'parent_id' => 1980,'action'=>1980,'menu_name'  => 'Edit', 'menu_url'  => 'fixed-asset-depreciation.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1983,'parent_id' => 1980,'action'=>1980,'menu_name'  => 'View', 'menu_url'  => 'fixed-asset-depreciation.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>1984,'parent_id' => 1980,'action'=>1980,'menu_name'  => 'Delete', 'menu_url'  => 'fixed-asset-depreciation.destroy','module_id'  => '6','created_at' => $time,'status' => 1),


                /*Acc year closing*/
                array('id'=>2000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Year Closing', 'menu_url'  => 'year-end.index','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2001,'parent_id' => 2000,'action'=>2000,'menu_name'  => 'Store', 'menu_url'  => 'year-end.store','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2002,'parent_id' => 2000,'action'=>2000,'menu_name'  => 'Edit', 'menu_url'  => 'year-end.edit','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2003,'parent_id' => 2000,'action'=>2000,'menu_name'  => 'View', 'menu_url'  => 'year-end.show','module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2004,'parent_id' => 2000,'action'=>2000,'menu_name'  => 'Delete', 'menu_url'  => 'year-end.destroy','module_id'  => '6','created_at' => $time,'status' => 1),


                /*Accounts Report*/
                array('id'=>2050,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Accounts Reports', 'menu_url'  => NULL, 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2051,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Journal Report', 'menu_url'  => 'journal-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2052,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Ledger Report', 'menu_url'  => 'ledger-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2053,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Subcode2 Wise Ledger Report', 'menu_url'  => 'ledger-subcode2-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2054,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Trial Balance', 'menu_url'  => 'trial-balance-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2055,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Income Statement', 'menu_url'  => 'income-statement-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2056,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Expense Statement', 'menu_url'  => 'expense-statement-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2057,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Balance Sheet', 'menu_url'  => 'balance-sheet-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2058,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Daily Statement', 'menu_url'  => 'daily-statement-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2059,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Profit & Loss Statement', 'menu_url'  => 'loss-profit-statement-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
                array('id'=>2060,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Bank Reconcilation Report', 'menu_url'  => 'bank-reconcilation-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
				array('id'=>2061,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Cash Book Report', 'menu_url'  => 'cash-book-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),
				array('id'=>2062,'parent_id' => 2050,'action'=> NULL,'menu_name'  => 'Bank Statement', 'menu_url'  => 'bank-statement-report.index', 'module_id'  => '6','created_at' => $time,'status' => 1),



                /***** LC  section***************/
                //START : LC Setup
                array('id'=>2200,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Setup', 'menu_url'  => '', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2210,'parent_id' => 2200,'action'=>NULL,'menu_name'  => 'LC Cost Name Category', 'menu_url'  => 'lc-cost-name-category-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2211,'parent_id' => 2210,'action'=> 2210,'menu_name'  => 'Add', 'menu_url'  => 'lc-cost-name-category-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2212,'parent_id' => 2210,'action'=> 2210,'menu_name'  => 'Edit', 'menu_url'  => 'lc-cost-name-category-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2213,'parent_id' => 2210,'action'=> 2210,'menu_name'  => 'Delete', 'menu_url'  => 'lc-cost-name-category-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2214,'parent_id' => 2210,'action'=> 2210,'menu_name'  => 'View', 'menu_url'  => 'lc-cost-name-category-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2220,'parent_id' => 2200,'action'=>NULL,'menu_name'  => 'LC Cost Name', 'menu_url'  => 'lc-cost-name-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2221,'parent_id' => 2220,'action'=> 2220,'menu_name'  => 'Add', 'menu_url'  => 'lc-cost-name-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2222,'parent_id' => 2220,'action'=> 2220,'menu_name'  => 'Edit', 'menu_url'  => 'lc-cost-name-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2223,'parent_id' => 2220,'action'=> 2220,'menu_name'  => 'Delete', 'menu_url'  => 'lc-cost-name-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2224,'parent_id' => 2220,'action'=> 2220,'menu_name'  => 'View', 'menu_url'  => 'lc-cost-name-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2230,'parent_id' => 2200,'action'=>NULL,'menu_name'  => 'LC Custom Duty Cost Name', 'menu_url'  => 'lc-custom-duty-cost-name-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2231,'parent_id' => 2230,'action'=> 2230,'menu_name'  => 'Add', 'menu_url'  => 'lc-custom-duty-cost-name-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2232,'parent_id' => 2230,'action'=> 2230,'menu_name'  => 'Edit', 'menu_url'  => 'lc-custom-duty-cost-name-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2233,'parent_id' => 2230,'action'=> 2230,'menu_name'  => 'Delete', 'menu_url'  => 'lc-custom-duty-cost-name-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2234,'parent_id' => 2230,'action'=> 2230,'menu_name'  => 'View', 'menu_url'  => 'lc-custom-duty-cost-name-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2240,'parent_id' => 2200,'action'=>NULL,'menu_name'  => 'LC C&F Agent / Party', 'menu_url'  => 'lc-cnf-agent-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2241,'parent_id' => 2240,'action'=> 2240,'menu_name'  => 'Add', 'menu_url'  => 'lc-cnf-agent-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2242,'parent_id' => 2240,'action'=> 2240,'menu_name'  => 'Edit', 'menu_url'  => 'lc-cnf-agent-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2243,'parent_id' => 2240,'action'=> 2240,'menu_name'  => 'Delete', 'menu_url'  => 'lc-cnf-agent-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2244,'parent_id' => 2240,'action'=> 2240,'menu_name'  => 'View', 'menu_url'  => 'lc-cnf-agent-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id' => 2250, 'parent_id' => 2200, 'action' => NULL, 'menu_name' => 'LC Voucher Type', 'menu_url' => 'lc-voucher-type.index', 'module_id' => '7', 'created_at' => $time, 'status' => 1),
                array('id' => 2251, 'parent_id' => 2250, 'action' => 2250, 'menu_name' => 'Add', 'menu_url' => 'lc-voucher-type.create', 'module_id' => '7', 'created_at' => $time, 'status' => 1),
                array('id' => 2252, 'parent_id' => 2250, 'action' => 2250, 'menu_name' => 'Edit', 'menu_url' => 'lc-voucher-type.edit', 'module_id' => '7', 'created_at' => $time, 'status' => 1),
                array('id' => 2253, 'parent_id' => 2250, 'action' => 2250, 'menu_name' => 'Delete', 'menu_url' => 'lc-voucher-type.destroy', 'module_id' => '7', 'created_at' => $time, 'status' => 1),
                array('id' => 2254, 'parent_id' => 2250, 'action' => 2250, 'menu_name' => 'View', 'menu_url' => 'lc-voucher-type.show', 'module_id' => '7', 'created_at' => $time, 'status' => 1),
                //END : LC Setup


                /*Lc Section */

                array('id'=>2300,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Work Order Import', 'menu_url'  => 'work-order-import.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2301,'parent_id' => 2300,'action'=> 2300,'menu_name'  => 'Add', 'menu_url'  => 'work-order-import.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2302,'parent_id' => 2300,'action'=> 2300,'menu_name'  => 'Edit', 'menu_url'  => 'work-order-import.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2303,'parent_id' => 2300,'action'=> 2300,'menu_name'  => 'Delete', 'menu_url'  => 'work-order-import.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2304,'parent_id' => 2300,'action'=> 2300,'menu_name'  => 'View', 'menu_url'  => 'work-order-import.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2305,'parent_id' => 2300,'action'=> 2300,'menu_name'  => 'Approve', 'menu_url'  => 'work-order-import.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2310,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Proforma Invoice Entry', 'menu_url'  => 'proforma-invoice-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2311,'parent_id' => 2310,'action'=> 2310,'menu_name'  => 'Add', 'menu_url'  => 'proforma-invoice-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2312,'parent_id' => 2310,'action'=> 2310,'menu_name'  => 'Edit', 'menu_url'  => 'proforma-invoice-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2313,'parent_id' => 2310,'action'=> 2310,'menu_name'  => 'Delete', 'menu_url'  => 'proforma-invoice-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2314,'parent_id' => 2310,'action'=> 2310,'menu_name'  => 'Approve', 'menu_url'  => 'proforma-invoice-entry.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2320,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC Opening Section', 'menu_url'  => 'lc-opening-section.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2321,'parent_id' => 2320,'action'=> 2320,'menu_name'  => 'Add', 'menu_url'  => 'lc-opening-section.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2322,'parent_id' => 2320,'action'=> 2320,'menu_name'  => 'Edit', 'menu_url'  => 'lc-opening-section.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2323,'parent_id' => 2320,'action'=> 2320,'menu_name'  => 'Delete', 'menu_url'  => 'lc-opening-section.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2324,'parent_id' => 2320,'action'=> 2320,'menu_name'  => 'View', 'menu_url'  => 'lc-opening-section.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2325,'parent_id' => 2320,'action'=> 2320,'menu_name'  => 'Approve', 'menu_url'  => 'lc-opening-section.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2330,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC Insurance', 'menu_url'  => 'lc-insurance.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2331,'parent_id' => 2330,'action'=> 2330,'menu_name'  => 'Add', 'menu_url'  => 'lc-insurance.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2332,'parent_id' => 2330,'action'=> 2330,'menu_name'  => 'Edit', 'menu_url'  => 'lc-insurance.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2333,'parent_id' => 2330,'action'=> 2330,'menu_name'  => 'Delete', 'menu_url'  => 'lc-insurance.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2334,'parent_id' => 2330,'action'=> 2330,'menu_name'  => 'View', 'menu_url'  => 'lc-insurance.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2335,'parent_id' => 2330,'action'=> 2330,'menu_name'  => 'Approve', 'menu_url'  => 'lc-insurance.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2340,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC Margin Entry', 'menu_url'  => 'lc-cf-value-margin-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2341,'parent_id' => 2340,'action'=> 2340,'menu_name'  => 'Add', 'menu_url'  => 'lc-cf-value-margin-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2342,'parent_id' => 2340,'action'=> 2340,'menu_name'  => 'Edit', 'menu_url'  => 'lc-cf-value-margin-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2343,'parent_id' => 2340,'action'=> 2340,'menu_name'  => 'Delete', 'menu_url'  => 'lc-cf-value-margin-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2344,'parent_id' => 2340,'action'=> 2340,'menu_name'  => 'View', 'menu_url'  => 'lc-cf-value-margin-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2345,'parent_id' => 2340,'action'=> 2340,'menu_name'  => 'Approve', 'menu_url'  => 'lc-cf-value-margin-entry.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2350,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Commercial Invoice Entry', 'menu_url'  => 'commercial-invoice-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2351,'parent_id' => 2350,'action'=> 2350,'menu_name'  => 'Add', 'menu_url'  => 'commercial-invoice-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2352,'parent_id' => 2350,'action'=> 2350,'menu_name'  => 'Edit', 'menu_url'  => 'commercial-invoice-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2353,'parent_id' => 2350,'action'=> 2350,'menu_name'  => 'Delete', 'menu_url'  => 'commercial-invoice-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2354,'parent_id' => 2350,'action'=> 2350,'menu_name'  => 'View', 'menu_url'  => 'commercial-invoice-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2355,'parent_id' => 2350,'action'=> 2350,'menu_name'  => 'Approve', 'menu_url'  => 'commercial-invoice-entry.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2360,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LATR Entry', 'menu_url'  => 'latr-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2361,'parent_id' => 2360,'action'=> 2360,'menu_name'  => 'Add', 'menu_url'  => 'latr-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2362,'parent_id' => 2360,'action'=> 2360,'menu_name'  => 'Edit', 'menu_url'  => 'latr-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2363,'parent_id' => 2360,'action'=> 2360,'menu_name'  => 'Delete', 'menu_url'  => 'latr-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2364,'parent_id' => 2360,'action'=> 2360,'menu_name'  => 'View', 'menu_url'  => 'latr-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2365,'parent_id' => 2360,'action'=> 2360,'menu_name'  => 'Approve', 'menu_url'  => 'latr-entry.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2370,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Custom Duty Charge Entry', 'menu_url'  => 'custom-duty-charge-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2371,'parent_id' => 2370,'action'=> 2370,'menu_name'  => 'Add', 'menu_url'  => 'custom-duty-charge-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2372,'parent_id' => 2370,'action'=> 2370,'menu_name'  => 'Edit', 'menu_url'  => 'custom-duty-charge-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2373,'parent_id' => 2370,'action'=> 2370,'menu_name'  => 'Delete', 'menu_url'  => 'custom-duty-charge-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2374,'parent_id' => 2370,'action'=> 2370,'menu_name'  => 'View', 'menu_url'  => 'custom-duty-charge-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2375,'parent_id' => 2370,'action'=> 2370,'menu_name'  => 'Approve', 'menu_url'  => 'custom-duty-charge-entry.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2380,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Others Charge Entry', 'menu_url'  => 'others-charge-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2381,'parent_id' => 2380,'action'=> 2380,'menu_name'  => 'Add', 'menu_url'  => 'others-charge-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2382,'parent_id' => 2380,'action'=> 2380,'menu_name'  => 'Edit', 'menu_url'  => 'others-charge-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2383,'parent_id' => 2380,'action'=> 2380,'menu_name'  => 'Delete', 'menu_url'  => 'others-charge-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2384,'parent_id' => 2380,'action'=> 2380,'menu_name'  => 'View', 'menu_url'  => 'others-charge-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2385,'parent_id' => 2380,'action'=> 2380,'menu_name'  => 'Approve', 'menu_url'  => 'others-charge-entry.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2388,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC CI Landed Cost', 'menu_url'  => 'lc-ci-landed-cost.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>20392,'parent_id' => 2388,'action'=> 2388,'menu_name'  => 'Edit', 'menu_url'  => 'lc-ci-landed-cost.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>20394,'parent_id' => 2388,'action'=> 2388,'menu_name'  => 'View', 'menu_url'  => 'lc-ci-landed-cost.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>20395,'parent_id' => 2388,'action'=> 2388,'menu_name'  => 'Approve', 'menu_url'  => 'lc-ci-landed-cost.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2390,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC Stock Entry', 'menu_url'  => 'lc-stock-entry.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2391,'parent_id' => 2390,'action'=> 2390,'menu_name'  => 'Add', 'menu_url'  => 'lc-stock-entry.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2392,'parent_id' => 2390,'action'=> 2390,'menu_name'  => 'Edit', 'menu_url'  => 'lc-stock-entry.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2393,'parent_id' => 2390,'action'=> 2390,'menu_name'  => 'Delete', 'menu_url'  => 'lc-stock-entry.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2394,'parent_id' => 2390,'action'=> 2390,'menu_name'  => 'View', 'menu_url'  => 'lc-stock-entry.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2395,'parent_id' => 2390,'action'=> 2390,'menu_name'  => 'Approve', 'menu_url'  => 'lc-stock-entry.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),


                array('id'=>2400,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC Closing', 'menu_url'  => 'lc-closing.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2401,'parent_id' => 2400,'action'=> 2400,'menu_name'  => 'Add', 'menu_url'  => 'lc-closing.create', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2402,'parent_id' => 2400,'action'=> 2400,'menu_name'  => 'Edit', 'menu_url'  => 'lc-closing.edit', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2403,'parent_id' => 2400,'action'=> 2400,'menu_name'  => 'Delete', 'menu_url'  => 'lc-closing.destroy', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2404,'parent_id' => 2400,'action'=> 2400,'menu_name'  => 'View', 'menu_url'  => 'lc-closing.show', 'module_id'  => '7','created_at' => $time,'status' => 1),
                array('id'=>2405,'parent_id' => 2400,'action'=> 2400,'menu_name'  => 'Approve', 'menu_url'  => 'lc-closing.approve', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2410,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'LC Reports', 'menu_url'  => NULL, 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2411,'parent_id' => 2410,'action'=> NULL,'menu_name'  => 'LC Report', 'menu_url'  => 'lc-report.index', 'module_id'  => '7','created_at' => $time,'status' => 1),
                    array('id'=>2412,'parent_id' => 2411,'action'=> 2411,'menu_name'  => 'View Landed Cost', 'menu_url'  => 'lc-report.show', 'module_id'  => '7','created_at' => $time,'status' => 1),

                array('id'=>2413,'parent_id' => 2410,'action'=> NULL,'menu_name'  => 'LC Ledger Report', 'menu_url'  => 'lc-ledger-report.index', 'module_id'  => '7','created_at' => $time,'status' => 1),



                /***** HR  section***************/


                // HR Setup Start
                array('id'=>22000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Setup', 'menu_url'  => NULL, 'module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>22100,'parent_id' => 22000,'action'=>NULL,'menu_name'  => 'Unit', 'menu_url'  => 'unit.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22110,'parent_id' => 22100,'action'=>22100,'menu_name'  => 'Store', 'menu_url'  => 'unit.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22120,'parent_id' => 22100,'action'=>22100,'menu_name'  => 'Edit', 'menu_url'  => 'unit.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22130,'parent_id' => 22100,'action'=>22100,'menu_name'  => 'Delete', 'menu_url'  => 'unit.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>22150,'parent_id' => 22000,'action'=>NULL,'menu_name'  => 'Division', 'menu_url'  => 'division.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22160,'parent_id' => 22150,'action'=>22150,'menu_name'  => 'Store', 'menu_url'  => 'division.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22170,'parent_id' => 22150,'action'=>22150,'menu_name'  => 'Edit', 'menu_url'  => 'division.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22180,'parent_id' => 22150,'action'=>22150,'menu_name'  => 'Delete', 'menu_url'  => 'division.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>22200,'parent_id' => 22000,'action'=>NULL,'menu_name'  => 'Department', 'menu_url'  => 'department.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22210,'parent_id' => 22200,'action'=>22200,'menu_name'  => 'Store', 'menu_url'  => 'department.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22220,'parent_id' => 22200,'action'=>22200,'menu_name'  => 'Edit', 'menu_url'  => 'department.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>22230,'parent_id' => 22200,'action'=>22200,'menu_name'  => 'Delete', 'menu_url'  => 'department.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id' => 22300, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Team', 'menu_url' => 'team.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22310, 'parent_id' => 22300, 'action' => 22300, 'menu_name' => 'Store', 'menu_url' => 'team.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22320, 'parent_id' => 22300, 'action' => 22300, 'menu_name' => 'Edit', 'menu_url' => 'team.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22330, 'parent_id' => 22300, 'action' => 22300, 'menu_name' => 'Delete', 'menu_url' => 'team.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22350, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Designation', 'menu_url' => 'designation.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22360, 'parent_id' => 22350, 'action' => 22350, 'menu_name' => 'Store', 'menu_url' => 'designation.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22370, 'parent_id' => 22350, 'action' => 22350, 'menu_name' => 'Edit', 'menu_url' => 'designation.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22380, 'parent_id' => 22350, 'action' => 22350, 'menu_name' => 'Delete', 'menu_url' => 'designation.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22400, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Section', 'menu_url' => 'section.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22410, 'parent_id' => 22400, 'action' => 22400, 'menu_name' => 'Store', 'menu_url' => 'section.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22420, 'parent_id' => 22400, 'action' => 22400, 'menu_name' => 'Edit', 'menu_url' => 'section.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22430, 'parent_id' => 22400, 'action' => 22400, 'menu_name' => 'Delete', 'menu_url' => 'section.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22450, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Line', 'menu_url' => 'line.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22460, 'parent_id' => 22450, 'action' => 22450, 'menu_name' => 'Store', 'menu_url' => 'line.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22470, 'parent_id' => 22450, 'action' => 22450, 'menu_name' => 'Edit', 'menu_url' => 'line.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22480, 'parent_id' => 22450, 'action' => 22450, 'menu_name' => 'Delete', 'menu_url' => 'line.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22500, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Floor', 'menu_url' => 'floor.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22510, 'parent_id' => 22500, 'action' => 22500, 'menu_name' => 'Store', 'menu_url' => 'floor.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22520, 'parent_id' => 22500, 'action' => 22500, 'menu_name' => 'Edit', 'menu_url' => 'floor.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22530, 'parent_id' => 22500, 'action' => 22500, 'menu_name' => 'Delete', 'menu_url' => 'floor.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22550, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Position', 'menu_url' => 'position.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22560, 'parent_id' => 22550, 'action' => 22550, 'menu_name' => 'Store', 'menu_url' => 'position.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22570, 'parent_id' => 22550, 'action' => 22550, 'menu_name' => 'Edit', 'menu_url' => 'position.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22580, 'parent_id' => 22550, 'action' => 22550, 'menu_name' => 'Delete', 'menu_url' => 'position.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22600, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Operation', 'menu_url' => 'operation.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22610, 'parent_id' => 22600, 'action' => 22600, 'menu_name' => 'Store', 'menu_url' => 'operation.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22620, 'parent_id' => 22600, 'action' => 22600, 'menu_name' => 'Edit', 'menu_url' => 'operation.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22630, 'parent_id' => 22600, 'action' => 22600, 'menu_name' => 'Delete', 'menu_url' => 'operation.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22650, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Employee Level', 'menu_url' => 'employee-level.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22660, 'parent_id' => 22650, 'action' => 22650, 'menu_name' => 'Store', 'menu_url' => 'employee-level.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22670, 'parent_id' => 22650, 'action' => 22650, 'menu_name' => 'Edit', 'menu_url' => 'employee-level.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22680, 'parent_id' => 22650, 'action' => 22650, 'menu_name' => 'Delete', 'menu_url' => 'employee-level.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22685, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Employee Type', 'menu_url' => 'employee-type.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22686, 'parent_id' => 22685, 'action' => 22685, 'menu_name' => 'Store', 'menu_url' => 'employee-type.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22687, 'parent_id' => 22685, 'action' => 22685, 'menu_name' => 'Edit', 'menu_url' => 'employee-type.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22688, 'parent_id' => 22685, 'action' => 22685, 'menu_name' => 'Delete', 'menu_url' => 'employee-type.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22700, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Shift Schedule', 'menu_url' => 'shift-schedule.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22710, 'parent_id' => 22700, 'action' => 22700, 'menu_name' => 'Store', 'menu_url' => 'shift-schedule.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22720, 'parent_id' => 22700, 'action' => 22700, 'menu_name' => 'Edit', 'menu_url' => 'shift-schedule.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22730, 'parent_id' => 22700, 'action' => 22700, 'menu_name' => 'Delete', 'menu_url' => 'shift-schedule.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22750, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Shift Manage', 'menu_url' => 'shift-manage.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22760, 'parent_id' => 22750, 'action' => 22750, 'menu_name' => 'Store', 'menu_url' => 'shift-manage.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22770, 'parent_id' => 22750, 'action' => 22750, 'menu_name' => 'Edit', 'menu_url' => 'shift-manage.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22780, 'parent_id' => 22750, 'action' => 22750, 'menu_name' => 'Delete', 'menu_url' => 'shift-manage.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id' => 22790, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Salary Grade', 'menu_url' => 'salary-grade.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22791, 'parent_id' => 22790, 'action' => 22790, 'menu_name' => 'Store', 'menu_url' => 'salary-grade.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22792, 'parent_id' => 22790, 'action' => 22790, 'menu_name' => 'Edit', 'menu_url' => 'salary-grade.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22793, 'parent_id' => 22790, 'action' => 22790, 'menu_name' => 'Delete', 'menu_url' => 'salary-grade.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),



                array('id' => 22800, 'parent_id' => 22000, 'action' => NULL, 'menu_name' => 'Bonus Setup', 'menu_url' => 'bonus.index', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22810, 'parent_id' => 22800, 'action' => 22800, 'menu_name' => 'Store', 'menu_url' => 'bonus.store', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22820, 'parent_id' => 22800, 'action' => 22800, 'menu_name' => 'Edit', 'menu_url' => 'bonus.edit', 'module_id' => '8', 'created_at' => $time,'status' => 1),
                array('id' => 22830, 'parent_id' => 22800, 'action' => 22800, 'menu_name' => 'Delete', 'menu_url' => 'bonus.destroy', 'module_id' => '8', 'created_at' => $time,'status' => 1),

                array('id'=>25200,'parent_id' => 22000,'action'=>NULL,'menu_name'  => 'Public Holiday(Fixed)', 'menu_url'  => 'public-holiday.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25210,'parent_id' => 25200,'action'=>25200,'menu_name'  => 'Store', 'menu_url'  => 'public-holiday.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25220,'parent_id' => 25200,'action'=>25200,'menu_name'  => 'Edit', 'menu_url'  => 'public-holiday.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25230,'parent_id' => 25200,'action'=>25200,'menu_name'  => 'Delete', 'menu_url'  => 'public-holiday.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>25300,'parent_id' => 22000,'action'=>NULL,'menu_name'  => 'Weekend', 'menu_url'  => 'weekly-holiday.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25310,'parent_id' => 25300,'action'=>25300,'menu_name'  => 'Store', 'menu_url'  => 'weekly-holiday.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25320,'parent_id' => 25300,'action'=>25300,'menu_name'  => 'Edit', 'menu_url'  => 'weekly-holiday.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25330,'parent_id' => 25300,'action'=>25300,'menu_name'  => 'Delete', 'menu_url'  => 'weekly-holiday.destroy','module_id'  => '8','created_at' => $time,'status' => 1),


                array('id'=>25400,'parent_id' => 22000,'action'=>NULL,'menu_name'  => 'Leave Type', 'menu_url'  => 'leave-type.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25410,'parent_id' => 25400,'action'=>25400,'menu_name'  => 'Store', 'menu_url'  => 'leave-type.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25420,'parent_id' => 25400,'action'=>25400,'menu_name'  => 'Edit', 'menu_url'  => 'leave-type.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25430,'parent_id' => 25400,'action'=>25400,'menu_name'  => 'Delete', 'menu_url'  => 'leave-type.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>25500,'parent_id' => 22000,'action'=>NULL,'menu_name'  => 'Earn Leave Configuration', 'menu_url'  => 'earn-leave-configuration.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25510,'parent_id' => 25500,'action'=>25500,'menu_name'  => 'Store', 'menu_url'  => 'earn-leave-configuration.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25520,'parent_id' => 25500,'action'=>25500,'menu_name'  => 'Edit', 'menu_url'  => 'earn-leave-configuration.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25530,'parent_id' => 25500,'action'=>25500,'menu_name'  => 'Delete', 'menu_url'  => 'earn-leave-configuration.destroy','module_id'  => '8','created_at' => $time,'status' => 1),


                // HR Setup End

                array('id'=>23000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Entry Panel', 'menu_url'  => NULL, 'module_id'  => '8','created_at' => $time,'status' => 1),

                /*array('id'=>2330,'parent_id' => 2300,'action'=>NULL,'menu_name'  => 'Branch', 'menu_url'  => 'branch.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>2331,'parent_id' => 2330,'action'=>2330,'menu_name'  => 'Store', 'menu_url'  => 'branch.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>2332,'parent_id' => 2330,'action'=>2330,'menu_name'  => 'Edit', 'menu_url'  => 'branch.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>2333,'parent_id' => 2330,'action'=>2330,'menu_name'  => 'Delete', 'menu_url'  => 'branch.destroy','module_id'  => '8','created_at' => $time,'status' => 1),*/

                array('id'=>23400,'parent_id' => 23000,'action'=>NULL,'menu_name'  => 'Employee Info', 'menu_url'  => 'manage-employee.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23410,'parent_id' => 23400,'action'=>23400,'menu_name'  => 'Store', 'menu_url'  => 'manage-employee.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23420,'parent_id' => 23400,'action'=>23400,'menu_name'  => 'Edit', 'menu_url'  => 'manage-employee.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23430,'parent_id' => 23400,'action'=>23400,'menu_name'  => 'Delete', 'menu_url'  => 'manage-employee.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>23500,'parent_id' => 23000,'action'=>NULL,'menu_name'  => 'Warning', 'menu_url'  => 'warning.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23510,'parent_id' => 23500,'action'=>23500,'menu_name'  => 'Store', 'menu_url'  => 'warning.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23520,'parent_id' => 23500,'action'=>23500,'menu_name'  => 'Edit', 'menu_url'  => 'warning.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23530,'parent_id' => 23500,'action'=>23500,'menu_name'  => 'Delete', 'menu_url'  => 'warning.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>23600,'parent_id' => 23000,'action'=>NULL,'menu_name'  => 'Left/Resign', 'menu_url'  => 'termination.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23610,'parent_id' => 23600,'action'=>23600,'menu_name'  => 'Store', 'menu_url'  => 'termination.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23620,'parent_id' => 23600,'action'=>23600,'menu_name'  => 'Edit', 'menu_url'  => 'termination.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23630,'parent_id' => 23600,'action'=>23600,'menu_name'  => 'Delete', 'menu_url'  => 'termination.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>23700,'parent_id' => 23000,'action'=>NULL,'menu_name'  => 'New To Regular', 'menu_url'  => 'employee-permanent.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23710,'parent_id' => 23700,'action'=>23700,'menu_name'  => 'Store', 'menu_url'  => 'employee-permanent.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23720,'parent_id' => 23700,'action'=>23700,'menu_name'  => 'Edit', 'menu_url'  => 'employee-permanent.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23730,'parent_id' => 23700,'action'=>23700,'menu_name'  => 'Delete', 'menu_url'  => 'employee-permanent.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>23800,'parent_id' => 23000,'action'=>NULL,'menu_name'  => 'Increament', 'menu_url'  => 'increament.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23810,'parent_id' => 23800,'action'=>23800,'menu_name'  => 'Store', 'menu_url'  => 'increament.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23820,'parent_id' => 23800,'action'=>23800,'menu_name'  => 'Edit', 'menu_url'  => 'increament.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>23830,'parent_id' => 23800,'action'=>23800,'menu_name'  => 'Delete', 'menu_url'  => 'increament.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>24000,'parent_id' => 23000,'action'=>NULL,'menu_name'  => 'Promotion', 'menu_url'  => 'promotion.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>24010,'parent_id' => 24000,'action'=>24000,'menu_name'  => 'Store', 'menu_url'  => 'promotion.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>24020,'parent_id' => 24000,'action'=>24000,'menu_name'  => 'Edit', 'menu_url'  => 'promotion.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>24030,'parent_id' => 24000,'action'=>24000,'menu_name'  => 'Delete', 'menu_url'  => 'promotion.destroy','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>25000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Leave', 'menu_url'  => NULL, 'module_id'  => '8','created_at' => $time,'status' => 1),

                /*array('id'=>2510,'parent_id' => 2500,'action'=>NULL,'menu_name'  => 'Manage Holiday', 'menu_url'  => 'manage-holiday.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>2511,'parent_id' => 2510,'action'=>2510,'menu_name'  => 'Store', 'menu_url'  => 'manage-holiday.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>2512,'parent_id' => 2510,'action'=>2510,'menu_name'  => 'Edit', 'menu_url'  => 'manage-holiday.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>2513,'parent_id' => 2510,'action'=>2510,'menu_name'  => 'Delete', 'menu_url'  => 'manage-holiday.destroy','module_id'  => '8','created_at' => $time,'status' => 1),*/



                array('id'=>25600,'parent_id' => 25000,'action'=>NULL,'menu_name'  => 'Apply For Leave', 'menu_url'  => 'apply-for-leave.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25610,'parent_id' => 25600,'action'=>25600,'menu_name'  => 'Store', 'menu_url'  => 'apply-for-leave.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25620,'parent_id' => 25600,'action'=>25600,'menu_name'  => 'Edit', 'menu_url'  => 'apply-for-leave.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25630,'parent_id' => 25600,'action'=>25600,'menu_name'  => 'Delete', 'menu_url'  => 'apply-for-leave.destroy','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>25631,'parent_id' => 25600,'action'=> 25600,'menu_name'  => 'Approve', 'menu_url'  => 'apply-for-leave.leave-approved', 'module_id'  => '8','created_at' => $time,'status' => 1),


                array('id'=>27000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Attendance', 'menu_url'  => NULL, 'module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>27100,'parent_id' => 27000,'action'=>NULL,'menu_name'  => 'Attendance Process', 'menu_url'  => 'manage-work-shift.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>27110,'parent_id' => 27100,'action'=>27100,'menu_name'  => 'Processing', 'menu_url'  => 'manage-work-shift.index','module_id'  => '8','created_at' => $time,'status' => 1),

                array('id'=>27200,'parent_id' => 27000,'action'=>NULL,'menu_name'  => 'Manual Attendance Entry', 'menu_url'  => 'manual-attendance-entry.index','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>27210,'parent_id' => 27200,'action'=>27200,'menu_name'  => 'Store', 'menu_url'  => 'manual-attendance-entry.store','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>27220,'parent_id' => 27200,'action'=>27200,'menu_name'  => 'Edit', 'menu_url'  => 'manual-attendance-entry.edit','module_id'  => '8','created_at' => $time,'status' => 1),
                array('id'=>27230,'parent_id' => 27200,'action'=>27200,'menu_name'  => 'Delete', 'menu_url'  => 'manual-attendance-entry.destroy','module_id'  => '8','created_at' => $time,'status' => 1),


                /***** Payroll  section***************/

                array('id'=>35000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Process', 'menu_url'  => NULL, 'module_id'  => '9','created_at' => $time,'status' => 1),

                array('id'=>35010,'parent_id' => 35000,'action'=>NULL,'menu_name'  => 'Salary Process', 'menu_url'  => 'salary-process.index','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>35020,'parent_id' => 35020,'action'=>35020,'menu_name'  => 'Process', 'menu_url'  => 'salary-process.index','module_id'  => '9','created_at' => $time,'status' => 1),

                array('id'=>35030,'parent_id' => 35000,'action'=>NULL,'menu_name'  => 'Bonus Process', 'menu_url'  => 'bonus-process.index','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>35040,'parent_id' => 35030,'action'=>35030,'menu_name'  => 'Process', 'menu_url'  => 'bonus-process.index','module_id'  => '9','created_at' => $time,'status' => 1),


                array('id'=>30000,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Report', 'menu_url'  => NULL, 'module_id'  => '9','created_at' => $time,'status' => 1),

                array('id'=>30500,'parent_id' => 30000,'action'=>NULL,'menu_name'  => 'Salary Sheet', 'menu_url'  => 'generate-salary-sheet.index','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>30510,'parent_id' => 30500,'action'=>30500,'menu_name'  => 'Store', 'menu_url'  => 'generate-salary-sheet.store','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>30520,'parent_id' => 30500,'action'=>30500,'menu_name'  => 'Edit', 'menu_url'  => 'generate-salary-sheet.edit','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>30530,'parent_id' => 30500,'action'=>30500,'menu_name'  => 'Delete', 'menu_url'  => 'generate-salary-sheet.destroy','module_id'  => '9','created_at' => $time,'status' => 1),

                array('id'=>31000,'parent_id' => 30000,'action'=>NULL,'menu_name'  => 'Salary Summery', 'menu_url'  => 'payroll-salary-summery.index','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>31010,'parent_id' => 31000,'action'=>31000,'menu_name'  => 'store', 'menu_url'  => 'payroll-salary-summery.store','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>31020,'parent_id' => 31000,'action'=>31000,'menu_name'  => 'Edit', 'menu_url'  => 'payroll-salary-summery.edit','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>31030,'parent_id' => 31000,'action'=>31000,'menu_name'  => 'Approve', 'menu_url'  => 'payroll-salary-summery.approve', 'module_id'  => '9','created_at' => $time,'status' => 1),

                array('id'=>31200,'parent_id' => 30000,'action'=>NULL,'menu_name'  => 'Bonus Summery', 'menu_url'  => 'payroll-bonus-summery.index','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>31210,'parent_id' => 31200,'action'=>31200,'menu_name'  => 'store', 'menu_url'  => 'payroll-bonus-summery.store','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>31220,'parent_id' => 31200,'action'=>31200,'menu_name'  => 'Edit', 'menu_url'  => 'payroll-bonus-summery.edit','module_id'  => '9','created_at' => $time,'status' => 1),
                array('id'=>31230,'parent_id' => 31200,'action'=>31200,'menu_name'  => 'Approve', 'menu_url'  => 'payroll-bonus-summery.approve', 'module_id'  => '9','created_at' => $time,'status' => 1),

                //Advanced Section
                array('id'=>111001,'parent_id' => 0,'action'=>NULL,'menu_name'  => 'Backups', 'menu_url'  => 'backup', 'module_id'  => '111','created_at' => $time,'status' => 1),
            ]
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
