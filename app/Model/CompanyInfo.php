<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $fillable = ['name','phone','mobile','email','fax','web','address','address2','logo'];
}
