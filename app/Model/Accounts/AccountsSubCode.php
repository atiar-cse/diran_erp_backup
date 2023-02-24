<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsSubCode extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['main_code_id','sub_code_title','sub_code','status','created_by','updated_by'];

    public  function main_code(){
        return $this->belongsTo(AccountsMainCode::class,'main_code_id');
    }

    public  function get_sub_code2_rows(){
        return $this->hasMany(AccountsSubCode2::class,'sub_code_id');
    }
}

