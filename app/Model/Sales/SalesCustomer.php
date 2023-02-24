<?php

namespace App\Model\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesCustomer extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['sales_customer_name','sales_customer_id','chart_ac_id','sales_customer_join_date','sales_customer_contact_person_name',
        'sales_customer_contact_person_job_title', 'sales_customer_business_phone','sales_customer_mobile','sales_customer_email',
        'sales_customer_web_address','sales_customer_credit_limit','sales_customer_office_address','sales_customer_narration',
        'status','created_by','updated_by'];
}
