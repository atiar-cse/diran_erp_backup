<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /* Primary Tables Independent Seeder Data*/
        $this->call(AccountsMainCodesTableSeeder::class);
        $this->call(AccountsSubCodesTableSeeder::class);
        $this->call(AccountsSubCode2sTableSeeder::class);
        $this->call(AccountsChartofAccountsTableSeeder::class);


        $this->call(CurrencyTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(ModuleTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(MenuTableSeeder::class);
        $this->call(MenuPermissionTableSeeder::class);
        $this->call(CompanyTableSeeder::class);

        $this->call(SupplierTableSeeder::class);
        $this->call(CustomerTableSeeder::class);

        $this->call(PurchaseWareHouseTableSeeder::class);
        $this->call(CostCenteTableSeeder::class);
        $this->call(ProductionBrandsTableSeeder::class);
        $this->call(ProductionProductsTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(AccountsBankNamesTableSeeder::class);
        $this->call(AccountsBankBranchesTableSeeder::class);
        $this->call(AccountsBankAccountsTableSeeder::class);

        $this->call(LcCustomDutyCostNameTableSeeder::class);
        $this->call(LcCostNameCategoryTableSeeder::class);
        $this->call(LcCostNameTableSeeder::class);
        $this->call(LcCnfAgentsTableSeeder::class);


        $this->call(HrDepartmentTableSeeder::class);
        $this->call(HrManageEmployeeTableSeeder::class);
        $this->call(HrDesignationTableSeeder::class);
        $this->call(HrBranchTableSeeder::class);


        $this->call(ProductionCategoriesTableSeeder::class);
        $this->call(ProductionMeasureUnitsTableSeeder::class);
        $this->call(PurchaseReturnTypesTableSeeder::class);
        $this->call(CompanyInfosTableSeeder::class);
        $this->call(HrUnitsTableSeeder::class);
        $this->call(HrEmployeeTypeTableSeeder::class);
        $this->call(HrReligionTableSeeder::class);
        $this->call(ProductionIndirectCostsTypesTableSeeder::class);
        $this->call(HrSalaryConfigurationsTableSeeder::class);
        $this->call(LeaveTitlesTableSeeder::class);
        $this->call(ConfigurationTableSeeder::class);
        $this->call(InventoryCurrentStocksTableSeeder::class);

        $this->call(LcVoucherTypeTableSeeder::class);

    }
}
