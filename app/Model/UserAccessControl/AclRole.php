<?php

namespace App\Model\UserAccessControl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class AclRole extends Model
{
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['id', 'role_name','created_by','modified_by'];

}
