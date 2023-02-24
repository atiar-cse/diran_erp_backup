<?php
namespace App\Lib\Enumerations;

class StockTransactionType
{
    public static $purchase_entry       = 0;
    public static $purchase_return      = 1;

    public static $sales_delivery       = 2;
    public static $sales_return         = 3;

    public static $prodution_req_rm     = 4;
    public static $prodution_forming    = 41;
    public static $prodution_shapping   = 42;
    public static $prodution_dryer      = 43;
    public static $prodution_glaze      = 44;
    public static $prodution_kiln       = 45;

    public static $prodution_testing    = 5;
    public static $prodution_assemble   = 6;
    public static $prodution_packing    = 7;

    public static $lc_stock_entry       = 8;

    public static $inventory_open       = 9;
    public static $inventory_adjust     = 10;
    public static $inventory_damage     = 11;
    public static $inventory_transfer   = 12;



    /*'0-pr-entry,
     1-pr-return,
     2-delivery-challan,
     3 -delicery return
     4 - production rm
     5 - production testing
     6 - production assmble
     7 - production packing
     8 - Lc Stock entry
     9 - Inventory Stockentry
     10 - Inventory adjust
     11 - Inventory damage'*/
}
