<?php

namespace App\Model\Sales;

use App\Model\Production\ProductionProducts;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceDetails extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'sales_invoice_id',
        'sales_invoice_details_product_id',
        'sales_invoice_details_description',
        'sales_invoice_details_unit',
        'sales_invoice_details_unit_price',
        'sales_invoice_details_qty',
        'sales_invoice_details_total_price'
    ];

    public function get_inv(){
        return $this->belongsTo(SalesInvoice::class,'sales_invoice_id')
            ->select('id','sales_invoices_no');
    }

    /*public function get_prod(){
        return $this->belongsTo(ProductionProducts::class,'sales_invoice_details_product_id')
            ->select('product_name','id')->with('measureunit');
    }*/

    public function get_prod()
    {
        return $this->belongsTo(ProductionProducts::class, 'sales_invoice_details_product_id')
            ->select('product_name', 'id');
    }
}