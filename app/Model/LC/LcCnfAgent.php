<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;

class LcCnfAgent extends Model
{
    protected $fillable = ['cnf_agent_name','cnf_agent_address','cnf_agent_contact_no','cnf_agent_email','narration','status','created_by','updated_by'];
}
