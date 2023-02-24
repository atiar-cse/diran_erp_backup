<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsBankBranch extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [ 'bank_branch_names','bank_branch_codes','bank_id',
                            'bank_branch_contact_no','bank_branch_address', 'status',
                            'created_by','updated_by'];
}
