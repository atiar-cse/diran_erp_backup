<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsSubCode2 extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['main_code_id','sub_code_id','sub_code_title2','sub_code2','status','created_by','updated_by'];

    public  function sub_code(){
        return $this->belongsTo(AccountsSubCode::class,'sub_code_id');
    }
}
