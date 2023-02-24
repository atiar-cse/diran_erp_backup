<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table ='configuration';

    protected $fillable =[
        'title',
        'config_key',
        'config_value',
        'status'
    ];
}
