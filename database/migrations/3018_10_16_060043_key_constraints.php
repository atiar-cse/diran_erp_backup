<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class KeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->foreign('role_id', 'fk_users_role_id')->references('id')->on('acl_roles')->onDelete('restrict');
        });
        Schema::table('acl_menus', function ($table) {
            $table->foreign('module_id', 'fk_menu_module_id')->references('id')->on('acl_modules')->onDelete('restrict');
        });
        Schema::table('acl_menu_permissions', function ($table) {
            $table->foreign('role_id', 'fk_acl_menu_role_id')->references('id')->on('acl_roles')->onDelete('restrict');
            $table->foreign('menu_id', 'fk_acl_menu_menu_id')->references('id')->on('acl_menus')->onDelete('restrict');
        });

/**
 *
 * @ Production Module
 *
 */
        Schema::table('production_products', function ($table) {
            $table->foreign('category_id', 'fk_product_category_id')->references('id')->on('production_categories')->onDelete('restrict');
            $table->foreign('measure_unit_id', 'fk_product_m_unit_id')->references('id')->on('production_measure_units')->onDelete('restrict');
            $table->foreign('brand_id', 'fk_product_brand_id')->references('id')->on('production_brands')->onDelete('restrict'); //New
            $table->foreign('country_id', 'fk_product_country_id')->references('id')->on('countries')->onDelete('restrict');  //New
        });

        Schema::table('production_rm_ratio_setups', function ($table) {
            $table->foreign('product_id', 'fk_rm_ratio_product_id')->references('id')->on('production_products')->onDelete('restrict');
        }); // New

        Schema::table('production_assembling_setups', function ($table) {
            $table->foreign('finishing_product_id', 'fk_prod_assemb_fin_product_id')->references('id')->on('production_products')->onDelete('restrict');
        }); // New A
        Schema::table('production_assembling_setup_details', function ($table) {
            $table->foreign('production_assembling_setup_id', 'fk_prod_assemb_setyp_id')->references('id')->on('production_assembling_setups')->onDelete('cascade');
            $table->foreign('fitting_product_id', 'fk_prod_assemb_fin_det_product_id')->references('id')->on('production_products')->onDelete('restrict');
        }); // New A
        Schema::table('production_current_stocks', function ($table) {
            $table->foreign('product_id', 'fk_prod_current_stck_product_id')->references('id')->on('production_products')->onDelete('restrict');
        }); // New A
        Schema::table('production_stock_month_ends', function ($table) {
            $table->foreign('product_id', 'fk_prod_stck_month_product_id')->references('id')->on('production_products')->onDelete('restrict');
        }); // New A

        Schema::table('production_indirect_cost_details', function ($table) {
            $table->foreign('prod_indir_costs_type_id', 'fk_prod_indir_costs_type_id')->references('id')->on('production_indirect_costs_types')->onDelete('restrict');
            $table->foreign('indir_cost_id', 'fk_prod_indir_cost_id')->references('id')->on('production_indirect_costs')->onDelete('cascade');
        });

        Schema::table('production_requisition_for_rms', function ($table) {
            $table->foreign('warehouse_id', 'fk_pro_req_rm_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
        });

        Schema::table('production_requisition_for_rm_details', function ($table) {
            $table->foreign('product_id', 'fk_pro_req_rmd_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('requisition_for_rm_id', 'fk_pro_req_rmd_req_rm_id')->references('id')->on('production_requisition_for_rms')->onDelete('cascade');
        });

        //new
        Schema::table('production_massbodies', function ($table) {
            $table->foreign('requisition_for_raw_material_id', 'fk_production_massbodies_raw_material_id')->references('id')->on('production_requisition_for_rms')->onDelete('restrict');
        });

        //new
        Schema::table('production_forming_details', function ($table) {
            $table->foreign('product_id', 'fk_forming_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('forming_section_id', 'fk_forming_details_forming_section_id')->references('id')->on('production_formings')->onDelete('cascade');
        });

        //new
        Schema::table('production_shapping_details', function ($table) {
            $table->foreign('product_id', 'fk_shapping_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('shapping_section_id', 'fk_shapping_details_shapping_section_id')->references('id')->on('production_shappings')->onDelete('cascade');
        });

        //new
        Schema::table('production_dryer_details', function ($table) {
            $table->foreign('product_id', 'fk_dryer_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('dryer_section_id', 'fk_dryer_details_dryer_section_id')->references('id')->on('production_dryers')->onDelete('cascade');
        });

        //new
        Schema::table('production_glaze_details', function ($table) {
            $table->foreign('product_id', 'fk_glaze_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('glaze_section_id', 'fk_glaze_details_glaze_section_id')->references('id')->on('production_glazes')->onDelete('cascade');
        });

        //new
        Schema::table('production_kiln_details', function ($table) {
            $table->foreign('product_id', 'fk_kiln_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('kiln_section_id', 'fk_kiln_details_kiln_section_id')->references('id')->on('production_kilns')->onDelete('cascade');
        });

        //new
        Schema::table('production_testings', function ($table) {
            $table->foreign('warehouse_id', 'fk_testings_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
        });

        //new
        Schema::table('production_testing_details', function ($table) {
            $table->foreign('product_id', 'fk_testing_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('testing_section_id', 'fk_testing_details_testing_section_id')->references('id')->on('production_testings')->onDelete('cascade');
        });

        //new
        Schema::table('production_finished_stocks', function ($table) {
            $table->foreign('warehouse_id', 'fk_finished_stocks_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
        });

        //new
        Schema::table('production_finished_stock_details', function ($table) {
            $table->foreign('product_id', 'fk_finished_stock_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('finished_stock_section_id', 'fk_finished_stock_details_finished_stock_section_id')->references('id')->on('production_finished_stocks')->onDelete('cascade');
        });
        //new
        Schema::table('production_assemblings', function ($table) {
            $table->foreign('warehouse_id', 'fk_assemblings_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('finishing_product_id', 'fk_assemblings_finishing_product_id')->references('id')->on('production_finished_stocks')->onDelete('restrict');
        });

        //new
        Schema::table('production_assembling_details', function ($table) {
            $table->foreign('product_id', 'fk_assembling_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('assembling_section_id', 'fk_assembling_details_assembling_section_id')->references('id')->on('production_assemblings')->onDelete('cascade');
        });

        //new
        Schema::table('production_packings', function ($table) {
            $table->foreign('warehouse_id', 'fk_packings_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('finishing_product_id', 'fk_packing_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        //new
        Schema::table('production_packing_details', function ($table) {
            $table->foreign('product_id', 'fk_packing_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('packing_section_id', 'fk_packing_details_section_id')->references('id')->on('production_packings')->onDelete('cascade');
        });


/**
 *
 * @ Purchase Module
 *
 */
        Schema::table('purchase_supplier_entries', function ($table) {
            $table->foreign('chart_ac_id', 'fk_pur_supp_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

        //new
        Schema::table('purchase_cost_types', function ($table) {
            $table->foreign('chart_ac_id', 'fk_cost_types_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

        Schema::table('purchase_requisition_details', function ($table) {
            $table->foreign('pr_requisition_id', 'fk_pur_reqd_pr_requisition_id')->references('id')->on('purchase_requisitions')->onDelete('restrict');
            $table->foreign('pr_product_id', 'fk_pur_reqd_pr_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        Schema::table('purchase_order_receives', function ($table) {
            $table->foreign('po_warehouse_id', 'fk_pur_or_po_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('po_supplier_id', 'fk_pur_or_po_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
            $table->foreign('po_requisition_id', 'fk_pur_or_po_requisition_id')->references('id')->on('purchase_requisitions')->onDelete('restrict');
        });

        Schema::table('purchase_order_receive_details', function ($table) {
            $table->foreign('pod_order_id', 'fk_pur_ord_pod_order_id')->references('id')->on('purchase_order_receives')->onDelete('cascade');
            $table->foreign('pod_product_id', 'fk_pur_ord_pod_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        //new
        Schema::table('purchase_related_cost_entries', function ($table) {
            $table->foreign('po_order_receive_id', 'fk_related_cost_entries_po_order_receive_id')->references('id')->on('purchase_order_receives')->onDelete('restrict');
            $table->foreign('credit_ac_id', 'fk_related_cost_entries_credit_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });


        //new
        Schema::table('purchase_related_cost_entry_details', function ($table) {
            $table->foreign('po_cost_id', 'fk_related_cost_entry_po_cost_id')->references('id')->on('purchase_related_cost_entries')->onDelete('cascade');
            $table->foreign('po_cost_type_id', 'fk_related_cost_entry_details_cost_type_id')->references('id')->on('purchase_cost_types')->onDelete('restrict');
        });


        //new
        Schema::table('purchase_returns', function ($table) {
            $table->foreign('po_rtn_warehouse_id', 'fk_return_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('po_rtn_supplier_id', 'fk_return_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
            $table->foreign('po_rtn_issue_type_id', 'fk_return_issue_type_id')->references('id')->on('purchase_return_types')->onDelete('restrict');
        });

        //new
        Schema::table('purchase_return_details', function ($table) {
            $table->foreign('po_rtnd_return_id', 'fk_return_details_po_rtnd_return_id')->references('id')->on('purchase_returns')->onDelete('cascade');
            $table->foreign('po_rtnd_product_id', 'fk_return_details_po_rtnd_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });


/**
 *
 * @ Sales Module
 *
 */
        Schema::table('sales_customers', function ($table) {
            $table->foreign('chart_ac_id', 'fk_sl_sus_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

        //new
        Schema::table('sales_invoices', function ($table) {
            $table->foreign('sales_invoices_warehouse_id', 'fk_sales_invoices_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('sales_invoices_customer_id', 'fk_sales_invoices_customer_id')->references('id')->on('sales_customers')->onDelete('restrict');
        });

        //new
        Schema::table('sales_invoice_details', function ($table) {
            $table->foreign('sales_invoice_id', 'fk_invoice_details_sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
            $table->foreign('sales_invoice_details_product_id', 'fk_invoice_details_sales_invoice_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        //new
        Schema::table('sales_delivery_challans', function ($table) {
            $table->foreign('sales_warehouse_id', 'fk_sales_delivery_challans_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('sales_customer_id', 'fk_sales_delivery_challans_customer_id')->references('id')->on('sales_customers')->onDelete('restrict');
            $table->foreign('sales_invoice_id', 'fk_sales_delivery_challans_sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('restrict');
        });

        //new
        Schema::table('sales_delivery_challan_details', function ($table) {
            $table->foreign('sales_delivery_id', 'fk_challan_details_sales_sales_delivery_id')->references('id')->on('sales_delivery_challans')->onDelete('cascade');
            $table->foreign('sales_delivery_details_product_id', 'fk_challan_details_sales_delivery_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        //new
        Schema::table('sales_delivery_returns', function ($table) {
            $table->foreign('sales_delivery_return_warehouse_id', 'fk_sales_delivery_return_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('sales_delivery_return_customer_id', 'fk_sales_delivery_return_customer_id')->references('id')->on('sales_customers')->onDelete('restrict');
            $table->foreign('sales_delivery_return_return_types_id', 'fk_sales_delivery_return_return_types_id')->references('id')->on('purchase_return_types')->onDelete('restrict');
        });

        //new
        Schema::table('sales_delivery_return_details', function ($table) {
            $table->foreign('sales_delivery_return_id', 'fk_delivery_return_details_sales_delivery_return_id')->references('id')->on('sales_delivery_returns')->onDelete('cascade');
            $table->foreign('sales_delivery_return_details_product_id', 'fk_delivery_return_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

/**
 *
 * @ Inventory Module
 *
 */
        Schema::table('inventory_stock_transfers', function ($table) {
            $table->foreign('from_warehouse_id', 'fk_inventory_stock_transfers_from_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('to_warehouse_id', 'fk_inventory_stock_transfers_to_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
        });

        // New
        Schema::table('inventory_current_stocks', function ($table) {
            $table->foreign('inventory_current_stocks_warehouse_id', 'fk_inventory_current_stocks_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('inventory_current_stocks_product_id', 'fk_inventory_current_stocks_product_id')->references('id')->on('production_products')->onDelete('restrict');

        });

        //new
        Schema::table('inventory_stock_transfer_details', function ($table) {
            $table->foreign('transfer_id', 'fk_inventory_stock_transfer_details_transfer_id')->references('id')->on('inventory_stock_transfers')->onDelete('cascade');
            $table->foreign('product_id', 'fk_inventory_stock_transfer_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        // new
        Schema::table('inventory_stock_adjusts', function ($table) {
            $table->foreign('inventory_stock_adjusts_warehouse_id', 'fk_inventory_stock_adjusts_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
        });

        //new
        Schema::table('inventory_stock_adjust_details', function ($table) {
            $table->foreign('inventory_stock_adjust_id', 'fk_inventory_stock_adjust_details_inventory_stock_adjust_id')->references('id')->on('inventory_stock_adjusts')->onDelete('cascade');
            $table->foreign('inventory_stock_adjust_details_product_id', 'fk_inventory_stock_adjust_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        //new
        Schema::table('inventory_product_damages', function ($table) {
            $table->foreign('inventory_product_damages_warehouse_id', 'fk_inventory_product_damages_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
        });

        //new
        Schema::table('inventory_product_damage_details', function ($table) {
            $table->foreign('inventory_product_damage_id', 'fk_inventory_product_damage_details_damage_id')->references('id')->on('inventory_product_damages')->onDelete('cascade');
            $table->foreign('inventory_product_damage_details_product_id', 'fk_inventory_product_damage_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

/**
 *
 * @ Accounts Module
 *
 */
         //New A
        Schema::table('accounts_bank_payment_details_vouchers', function ($table) {
            $table->foreign('bank_payment_id', 'fk_acc_bank_pay_det_vou_bank_payment_id')->references('id')->on('accounts_bank_payment_vouchers')->onDelete('cascade');
            $table->foreign('debit_account_id', 'fk_acc_bank_pay_det_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_acc_bank_pay_det_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });
        //New A
        Schema::table('accounts_bank_receipt_details_vouchers', function ($table) {
            $table->foreign('bank_receipt_id', 'fk_acc_bank_rec_det_vou_bank_receipt_id')->references('id')->on('accounts_bank_receipt_vouchers')->onDelete('cascade');
            $table->foreign('debit_account_id', 'fk_acc_bank_rec_det_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_acc_bank_rec_det_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });
        //New A
        Schema::table('accounts_bank_transfer_deposits', function ($table) {
            $table->foreign('debit_account_id', 'fk_acc_bank_trans_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_acc_bank_trans_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });
        //New A
        Schema::table('accounts_cash_payment_details_vouchers', function ($table) {
            $table->foreign('cash_payment_id', 'fk_acc_cash_pay_det_vou_cash_payment_id')->references('id')->on('accounts_cash_payment_vouchers')->onDelete('cascade');
            $table->foreign('debit_account_id', 'fk_acc_cash_pay_det_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_acc_cash_pay_det_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        }); 
        //New A
        Schema::table('accounts_cash_receipt_details_vouchers', function ($table) {
            $table->foreign('cash_receipt_id', 'fk_acc_cash_rec_det_vou_cash_receipt_id')->references('id')->on('accounts_cash_receipt_vouchers')->onDelete('cascade');
            $table->foreign('debit_account_id', 'fk_acc_cash_rec_det_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_acc_cash_rec_det_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });  
        //New A
        Schema::table('accounts_contra_entry_vouchers', function ($table) {
            $table->foreign('debit_account_id', 'fk_acc_contra_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_acc_contra_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });  
        //New A
        Schema::table('accounts_fixed_asset_depreciation_details', function ($table) {
            $table->foreign('depreciation_id', 'fk_acc_fixed_asset_depreciation_id')->references('id')->on('accounts_fixed_asset_depreciations')->onDelete('cascade');
            $table->foreign('chart_acc_id', 'fk_acc_fixed_asset_chart_acc_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });   
        //New A
        Schema::table('accounts_journal_entry_details', function ($table) {
            $table->foreign('journal_entry_id')->references('id')->on('accounts_journal_entries')->onDelete('cascade');
            $table->foreign('char_of_account_id', 'fk_acc_journal_entry_chart_acc_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });   
        //New A
        Schema::table('accounts_monthly_closings', function ($table) {
            $table->foreign('chart_of_account_id', 'fk_acc_monthly_close_chart_acc_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });  
        //New A
        Schema::table('accounts_purchase_order_vouchers', function ($table) {
            $table->foreign('purchase_stock_id','fk_acc_pur_order_stock_id')->references('id')->on('purchase_order_receives')->onDelete('cascade');
            $table->foreign('supplier_id', 'fk_acc_acc_pur_order_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
        });  
        //New A
        Schema::table('accounts_purchase_order_voucher_details', function ($table) {
            $table->foreign('ac_pruchase_stock_voucher_id','fk_acc_pur_order_vou_stock_id')->references('id')->on('accounts_purchase_order_vouchers')->onDelete('cascade');
            $table->foreign('product_id', 'fk_acc_pur_ord_det_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });
        //New A
        Schema::table('accounts_purchase_return_vouchers', function ($table) {
            $table->foreign('supplier_id', 'fk_acc_acc_pur_return_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
        });  
        //New A
        Schema::table('accounts_purchase_return_voucher_details', function ($table) {
            $table->foreign('ac_pruchase_return_voucher_id','fk_acc_pur_ret_vou_stock_id')->references('id')->on('accounts_purchase_return_vouchers')->onDelete('cascade');
            $table->foreign('product_id', 'fk_acc_pur_ret_cou_det_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });
        //New A
        Schema::table('accounts_sales_invoice_return_vouchers', function ($table) {
            $table->foreign('customer_id', 'fk_acc_acc_sale_inv_return_cus_id')->references('id')->on('sales_customers')->onDelete('restrict');
        }); 
        //New A
        Schema::table('accounts_sales_invoice_return_voucher_details', function ($table) {
            $table->foreign('ac_sales_return_voucher_id','fk_acc_sales_ret_vou_stock_id')->references('id')->on('accounts_sales_invoice_return_vouchers')->onDelete('cascade');
            $table->foreign('product_id', 'fk_acc_sales_ret_vou_det_product_id')->references('id')->on('production_products')->onDelete('restrict');
        }); 
        //New A
        Schema::table('accounts_sales_invoice_voucher_details', function ($table) {
            $table->foreign('ac_sales_voucher_id','fk_acc_sales_inv_vou_id')->references('id')->on('accounts_sales_invoice_vouchers')->onDelete('cascade');
            $table->foreign('product_id', 'fk_acc_sales_inv_vou_det_product_id')->references('id')->on('production_products')->onDelete('restrict');
        }); 
        //New A
        Schema::table('accounts_year_ends', function ($table) {
            $table->foreign('chart_account_id', 'fk_acc_year_end_coa_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

        //New
        Schema::table('accounts_bank_branches', function ($table) {
            $table->foreign('bank_id', 'fk_accounts_bank_branches_bank_id')->references('id')->on('accounts_bank_names')->onDelete('cascade');
        });

        //New
        Schema::table('accounts_bank_accounts', function ($table) {
            $table->foreign('accounts_bank_id', 'fk_accounts_bank_accounts_accounts_bank_id')->references('id')->on('accounts_bank_names')->onDelete('restrict');
            $table->foreign('accounts_branch_id', 'fk_accounts_bank_accounts_accounts_branch_id')->references('id')->on('accounts_bank_branches')->onDelete('restrict');
           $table->foreign('chart_ac_id', 'fk_accounts_bank_accounts_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

        //New
        Schema::table('check_book_leafs', function ($table) {
            $table->foreign('chart_ac_id', 'fk_check_book_leafs_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

        //New
        Schema::table('check_book_leaf_gen_details', function ($table) {
            $table->foreign('check_book_leaf_id', 'fk_check_book_leaf_gen_details_bank_id')->references('id')->on('check_book_leafs')->onDelete('cascade');
            $table->foreign('chart_ac_id', 'fk_check_book_leaf_gen_details_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });


/**
 *
 * @ LC Module
 *
 */
        /// New
        Schema::table('lc_cost_name_entries', function ($table) {
            $table->foreign('cost_category_id', 'fk_cost_name_entries_cost_category_id')->references('id')->on('lc_cost_name_categories')->onDelete('cascade');
            $table->foreign('chart_ac_id', 'fk_cost_name_entries_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });


        //New
        Schema::table('lc_custom_duty_cost_name_entries', function ($table) {
            $table->foreign('chart_ac_id', 'fk_custom_duty_cost_name_entries_chart_ac_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });


        // New
        Schema::table('lc_work_order_imports', function ($table) {
            $table->foreign('supplier_id', 'fk_work_order_imports_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
        });


        // New
        Schema::table('lc_work_order_import_details', function ($table) {
            $table->foreign('work_order_import_id', 'fk_work_order_import_details_work_order_import_id')->references('id')->on('lc_work_order_imports')->onDelete('cascade');
            $table->foreign('product_id', 'fk_work_order_import_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('currency_id', 'fk_work_order_import_details_currency_id')->references('id')->on('currencies')->onDelete('restrict');
        });

        //New
        Schema::table('lc_proforma_invoice_entries', function ($table) {
            $table->foreign('work_order_id', 'fk_proforma_invoice_entries_work_order_id')->references('id')->on('lc_work_order_imports')->onDelete('restrict');
            $table->foreign('supplier_id', 'fk_proforma_invoice_entries_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
        });

        // New
        Schema::table('lc_proforma_invoice_details', function ($table) {
            $table->foreign('lc_proforma_invoice_id', 'fk_proforma_invoice_details_lc_proforma_invoice_id')->references('id')->on('lc_proforma_invoice_entries')->onDelete('cascade');
            $table->foreign('product_id', 'fk_proforma_invoice_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('currency_id', 'fk_proforma_invoice_details_currency_id')->references('id')->on('currencies')->onDelete('restrict');
           // $table->foreign('origin_of_goods', 'fk_proforma_invoice_details_origin_of_goods')->references('id')->on('countries')->onDelete('restrict');
        });

        // New
        Schema::table('lc_opening_sections', function ($table) {
            $table->foreign('supplier_id', 'fk_lc_opening_sections_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
            $table->foreign('proforma_invoice_id', 'fk_lc_opening_sections_proforma_invoice_id')->references('id')->on('lc_proforma_invoice_entries')->onDelete('restrict');
            $table->foreign('lc_opening_bank_id', 'fk_lc_opening_sections_lc_opening_bank_id')->references('id')->on('accounts_bank_names')->onDelete('restrict');
        });

        // New
        Schema::table('lc_insurances', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_insurances_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
        });

        // New
        Schema::table('lc_cf_value_margin_entries', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_cf_value_margin_entries_lc_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
            $table->foreign('supplier_id', 'fk_lc_cf_value_margin_entries_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
            $table->foreign('bank_id', 'fk_lc_cf_value_margin_entries_bank_id')->references('id')->on('accounts_bank_names')->onDelete('restrict');
        });

        // New
        Schema::table('lc_commercial_invoice_entries', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_commercial_invoice_entries_lc_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
        });

        // New
        Schema::table('lc_commercial_invoice_details', function ($table) {
            $table->foreign('lc_commercial_invoice_id', 'fk_lc_commercial_invoice_details_lc_commercial_invoice_id')->references('id')->on('lc_commercial_invoice_entries')->onDelete('cascade');
            $table->foreign('product_id', 'fk_lc_commercial_invoice_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('measure_unit_id', 'fk_lc_commercial_invoice_details_m_unit_id')->references('id')->on('production_measure_units')->onDelete('restrict');
        });

        // New
        Schema::table('lc_latr_entries', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_latr_entries_lc_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
            $table->foreign('lc_commercial_invoice_id', 'fk_lc_latr_entries_lc_commercial_invoice_id')->references('id')->on('lc_commercial_invoice_entries')->onDelete('restrict');
        });

        // New
        Schema::table('lc_latr_details', function ($table) {
            $table->foreign('lc_latr_payment_entry_id', 'fk_lc_latr_details_lc_latr_payment_entry_id')->references('id')->on('lc_latr_entries')->onDelete('cascade');
            $table->foreign('product_id', 'fk_lc_latr_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('measure_unit_id', 'fk_lc_latr_details_m_unit_id')->references('id')->on('production_measure_units')->onDelete('restrict');
        });

        // New
        Schema::table('lc_custom_duty_charge_entries', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_custom_duty_charge_entries_lc_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
            $table->foreign('lc_commercial_invoice_id', 'fk_lc_custom_duty_charge_entries_lc_commercial_invoice_id')->references('id')->on('lc_commercial_invoice_entries')->onDelete('restrict');
            $table->foreign('custom_duty_cost_id', 'fk_lc_custom_duty_charge_entries_custom_duty_cost_id')->references('id')->on('lc_custom_duty_cost_name_entries')->onDelete('restrict');
            $table->foreign('cnf_agent_id', 'fk_lc_custom_duty_charge_entries_cnf_agent_id')->references('id')->on('lc_cnf_agents')->onDelete('restrict');
        });

        // New
        Schema::table('lc_custom_duty_charge_details', function ($table) {
            $table->foreign('lc_custom_duty_entry_id', 'fk_lc_custom_duty_charge_details_lc_custom_duty_entry_id')->references('id')->on('lc_custom_duty_charge_entries')->onDelete('cascade');
            $table->foreign('product_id', 'fk_lc_custom_duty_charge_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
        });

        // New
        Schema::table('lc_others_charge_entries', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_others_charge_entries_lc_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
            $table->foreign('lc_commercial_invoice_id', 'fk_lc_others_charge_entries_lc_commercial_invoice_id')->references('id')->on('lc_commercial_invoice_entries')->onDelete('restrict');
            $table->foreign('cost_category_id', 'fk_lc_others_charge_entries_cost_category_id')->references('id')->on('lc_cost_name_categories')->onDelete('restrict');
        });


        // New lc_others_charge_details
        Schema::table('lc_others_charge_details', function ($table) {
            $table->foreign('lc_other_charge_entry_id', 'fk_lc_others_charge_details_lc_other_charge_entry_id')->references('id')->on('lc_others_charge_entries')->onDelete('cascade');
            $table->foreign('lc_cost_name_id', 'fk_lc_others_charge_details_lc_cost_name_id')->references('id')->on('lc_cost_name_entries')->onDelete('restrict');
        });

        // New
        Schema::table('lc_stock_entries', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_stock_entries_lc_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
            $table->foreign('lc_commercial_invoice_id', 'fk_lc_stock_entries_lc_commercial_invoice_id')->references('id')->on('lc_commercial_invoice_entries')->onDelete('restrict');
            $table->foreign('warehouse_id', 'fk_lc_stock_entries_warehouse_id')->references('id')->on('purchase_ware_houses')->onDelete('restrict');
            $table->foreign('supplier_id', 'fk_lc_stock_entries_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
        });

        // New
        Schema::table('lc_stock_entry_details', function ($table) {
            $table->foreign('lc_stock_entry_id', 'fk_lc_stock_entry_details_lc_stock_entry_id')->references('id')->on('lc_stock_entries')->onDelete('cascade');
            $table->foreign('product_id', 'fk_lc_stock_entry_details_product_id')->references('id')->on('production_products')->onDelete('restrict');
            $table->foreign('measure_unit_id', 'fk_lc_stock_entry_details_m_unit_id')->references('id')->on('production_measure_units')->onDelete('restrict');
        });

        // New
        Schema::table('lc_closings', function ($table) {
            $table->foreign('lc_opening_info_id', 'fk_lc_closings_lc_opening_info_id')->references('id')->on('lc_opening_sections')->onDelete('restrict');
            $table->foreign('supplier_id', 'fk_lc_closings_supplier_id')->references('id')->on('purchase_supplier_entries')->onDelete('restrict');
        });



/**
 *
 * @ HR Module
 *
 */
        Schema::table('hr_divisions', function ($table) {
            $table->foreign('unit_id', 'fk_hr_div_unit_id')->references('id')->on('hr_units')->onDelete('restrict');

        });
        Schema::table('hr_divisions', function ($table) {
            $table->foreign('head_person', 'fk_hr_head_person')->references('id')->on('hr_manage_employees')->onDelete('restrict');
        });

        Schema::table('hr_departments', function ($table) {
            $table->foreign('div_id', 'fk_hr_dept_div_id')->references('id')->on('hr_divisions')->onDelete('restrict');
        });

        Schema::table('hr_departments', function ($table) {
            $table->foreign('head_person', 'fk_hr_dept_head_person')->references('id')->on('hr_manage_employees')->onDelete('restrict');
        });


        Schema::table('hr_teams', function ($table) {
            $table->foreign('dept_id', 'fk_hr_team_dept_id')->references('id')->on('hr_departments')->onDelete('restrict');
        });
        Schema::table('hr_teams', function ($table) {
            $table->foreign('head_person', 'fk_hr_team_head_person')->references('id')->on('hr_manage_employees')->onDelete('restrict');
        });


        Schema::table('hr_designations', function ($table) {
            $table->foreign('employee_level_id', 'fk_hr_desig_employee_level_id')->references('id')->on('hr_employee_level')->onDelete('restrict');
        });
        Schema::table('hr_shift_manage', function ($table) {
            $table->foreign('shift_schedule_id', 'fk_hr_shift_manage_shift_schedule_id')
                ->references('id')->on('hr_shift_schedule')->onDelete('restrict');
        });


        Schema::table('hr_sections', function ($table) {
            $table->foreign('unit_id', 'fk_hr_section_unit_id')
                ->references('id')->on('hr_units')->onDelete('restrict');
        });

        Schema::table('hr_lines', function ($table) {
            $table->foreign('unit_id', 'fk_hr_line_unit_id')
                ->references('id')->on('hr_units')->onDelete('restrict');
        });

        Schema::table('hr_floors', function ($table) {
            $table->foreign('unit_id', 'fk_hr_floor_unit_id')
                ->references('id')->on('hr_units')->onDelete('restrict');
        });

        Schema::table('hr_positions', function ($table) {
            $table->foreign('unit_id', 'fk_hr_position_unit_id')
                ->references('id')->on('hr_units')->onDelete('restrict');
        });

        Schema::table('hr_operations', function ($table) {
            $table->foreign('unit_id', 'fk_hr_operation_unit_id')
                ->references('id')->on('hr_units')->onDelete('restrict');
        });

        Schema::table('hr_bonus', function ($table) {
            $table->foreign('unit_id', 'fk_hr_units_unit_id')
                ->references('id')->on('hr_units')->onDelete('restrict');
            $table->foreign('employee_type', 'fk_hr_employee_type_employee_type')
                ->references('id')->on('hr_employee_types')->onDelete('restrict');
        });

       // New
        Schema::table('hr_leave_types', function ($table) {
            $table->foreign('employee_type_id', 'fk_hr_leave_types_employee_type_id')->references('id')->on('hr_employee_types')->onDelete('restrict');
        });

       // New
        Schema::table('hr_manage_employees', function ($table) {
            $table->foreign('user_id', 'fk_hr_manage_employees_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('role_id', 'fk_hr_manage_employees_role_id')->references('id')->on('acl_roles')->onDelete('restrict');
            $table->foreign('department_id', 'fk_hr_manage_employees_department_id')->references('id')->on('hr_departments')->onDelete('restrict');
            $table->foreign('designation_id', 'fk_hr_manage_employees_designation_id')->references('id')->on('hr_designations')->onDelete('restrict');
            $table->foreign('branch_id', 'fk_hr_manage_employees_branch_id')->references('id')->on('hr_branches')->onDelete('restrict');
            $table->foreign('work_shift_id', 'fk_hr_manage_employees_work_shift_id')->references('id')->on('hr_manage_work_shifts')->onDelete('restrict');
            $table->foreign('hourly_pay_grade_id', 'fk_hr_manage_employees_hourly_pay_grade_id')->references('id')->on('payroll_hourly_pay_grades')->onDelete('restrict');
            $table->foreign('employee_type_id', 'fk_hr_manage_employees_employee_type_id')->references('id')->on('hr_employee_types')->onDelete('restrict');
            $table->foreign('team_id', 'fk_hr_manage_employees_team_id')->references('id')->on('hr_teams')->onDelete('restrict');
            $table->foreign('salary_grade_id', 'fk_hr_manage_employees_salary_grade_id')->references('id')->on('hr_salary_grades')->onDelete('restrict');
            $table->foreign('religion_id', 'fk_hr_manage_employees_religion_id')->references('id')->on('hr_religion')->onDelete('restrict');
        });

        // New
        Schema::table('hr_warnings', function ($table) {
            $table->foreign('warning_to_employee_id', 'fk_hr_warnings_warning_to_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
            $table->foreign('warning_by_employee_id', 'fk_hr_warnings_warning_by_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
        });

        // New
        Schema::table('hr_terminations', function ($table) {
            $table->foreign('employee_terminated_employee_id', 'fk_hr_terminations_employee_terminated_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
            $table->foreign('terminated_by_employee_id', 'fk_hr_terminations_terminated_by_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
        });

        // New
        Schema::table('hr_increments', function ($table) {
            $table->foreign('employee_id', 'fk_hr_increments_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
            $table->foreign('current_department_id', 'fk_hr_increments_current_department_id')->references('id')->on('hr_departments')->onDelete('restrict');
            $table->foreign('current_designation_id', 'fk_hr_increments_current_designation_id')->references('id')->on('hr_designations')->onDelete('restrict');
            //$table->foreign('current_pay_grade_id', 'fk_hr_increments_current_pay_grade_id')->references('id')->on('payroll_monthly_pay_grades')->onDelete('restrict');
        });

        // New
        Schema::table('hr_promotions', function ($table) {
            $table->foreign('employee_id', 'fk_hr_promotions_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
            $table->foreign('current_department_id', 'fk_hr_promotions_current_department_id')->references('id')->on('hr_departments')->onDelete('restrict');
            $table->foreign('current_designation_id', 'fk_hr_promotions_current_designation_id')->references('id')->on('hr_designations')->onDelete('restrict');
           // $table->foreign('current_pay_grade_id', 'fk_hr_promotions_current_pay_grade_id')->references('id')->on('payroll_monthly_pay_grades')->onDelete('restrict');
        });

        // New
        Schema::table('hr_apply_for_leaves', function ($table) {
            $table->foreign('employee_id', 'fk_hr_apply_for_leaves_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
            $table->foreign('leave_type_id', 'fk_hr_apply_for_leaves_leave_type_id')->references('id')->on('hr_leave_types')->onDelete('restrict');
        });

        // New
        Schema::table('hr_manual_attendance_entries', function ($table) {
            $table->foreign('employee_attendance_employee_id', 'fk_hr_manual_attendance_entries_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
            $table->foreign('employee_attendance_department_id', 'fk_hr_manual_attendance_entries_department_id')->references('id')->on('hr_departments')->onDelete('restrict');
            $table->foreign('employee_shift_id', 'fk_hr_manual_attendance_entries_employee_shift_id')->references('id')->on('hr_manage_work_shifts')->onDelete('restrict');
        });

/**
 *
 * @ Payroll Module
 *
 */
        // New
        Schema::table('payroll_salary_processes', function ($table) {
            $table->foreign('employee_id', 'fk_payroll_salary_processes_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
            $table->foreign('department_id', 'fk_payroll_salary_processes_department_id')->references('id')->on('hr_departments')->onDelete('restrict');
            $table->foreign('designation_id', 'fk_payroll_salary_processes_designation_id')->references('id')->on('hr_designations')->onDelete('restrict');
        });

        // New
        Schema::table('payroll_bonus_processes', function ($table) {
            $table->foreign('bonus_setting_id', 'fk_payroll_bonus_processes_bonus_setting_id')->references('id')->on('hr_bonus')->onDelete('restrict');
            $table->foreign('employee_id', 'fk_payroll_bonus_processes_employee_id')->references('id')->on('hr_manage_employees')->onDelete('restrict');
        });

        // New
        Schema::table('payroll_salary_summeries', function ($table) {
            $table->foreign('debit_account_id', 'fk_payroll_salary_summeries_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_payroll_salary_summeries_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

        //New
        Schema::table('payroll_bonus_summeries', function ($table) {
            $table->foreign('debit_account_id', 'fk_payroll_bonus_summeries_debit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
            $table->foreign('credit_account_id', 'fk_payroll_bonus_summeries_credit_account_id')->references('id')->on('accounts_chartof_accounts')->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
