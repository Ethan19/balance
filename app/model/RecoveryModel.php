<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RecoveryModel extends Model
{
	protected $table = 'recovery';
	protected $primaryKey = "recovery_id";
    protected $fillable = array('member_id','operator_type','channel_id','operator_id','change_balance','before_balance','after_balance','active_member','type','relation_id');

}
