<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = ['module_id','config_name','config_value','updated_by'];
}
