<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RecoveryModel extends Model
{
	protected $table = 'recovery';
	protected $primaryKey = "recovery_id";
    protected $fillable = array('member_id','operator_type','channel_id','operator_id','change_balance','before_balance','after_balance','active_member','type','relation_id');
    
    /**
     * [belongOneMember 关系模型]
     * 1.目标
     * 2.$this model的关联键
     * 3.目标的关联键
     * @author Ethan
     * @date   2017-04-26
     * @return [type]     [description]
     */
    public function member(){
        return $this->belongsTo('App\model\MemberModel',"member_id","member_id");
    }

    public function channel(){
        return $this->belongsTo('App\model\ChannelModel',"channel_id","channel_id");
    }

    public function operation(){
        return $this->belongsTo('App\model\OperationModel',"operator_id","operation_id");
    }
}
