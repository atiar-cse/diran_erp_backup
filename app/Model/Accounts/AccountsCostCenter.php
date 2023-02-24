<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsCostCenter extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['cost_center_name','status','created_by','updated_by'];
}
