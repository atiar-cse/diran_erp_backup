<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsBankName extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['accounts_bank_names','accounts_bank_codes','accounts_bank_address',
        'status','created_by','updated_by'];
}
