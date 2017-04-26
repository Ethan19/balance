<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class LogModel extends Model
{
	protected $table = 'log';
	protected $fillable = array('member_id','operator_type','channel_id','operator_id','change_balance','before_balance','after_balance','active_member','type','relation_id');
    //加入日志
	public static function addLog($data){
		self::create($data);
	}
}
