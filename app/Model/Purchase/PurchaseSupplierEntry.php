<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseSupplierEntry extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['purchase_supplier_name','purchase_supplier_id','chart_ac_id','purchase_supplier_join_date','purchase_supplier_contact_person_name',
        'purchase_supplier_contact_person_job_title', 'purchase_supplier_business_phone','purchase_supplier_mobile','purchase_supplier_email',
        'purchase_supplier_web_address','purchase_supplier_credit_limit','purchase_supplier_office_address','purchase_supplier_narration',
        'is_importer','status','created_by','updated_by'];
}
