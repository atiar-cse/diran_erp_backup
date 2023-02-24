<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrPublicHoliday extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['holiday_name','from_date','to_date','narration','status','created_by','updated_by'];
}
