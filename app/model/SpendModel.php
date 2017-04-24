<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class SpendModel extends Model
{
    //
	protected $table = 'spend';
	protected $primaryKey = "spend_id";
    protected $fillable = array('member_id','operator_type','channel_id','operator_id','change_balance','before_balance','after_balance','active_member','type','relation_id');
    /**
     * [addIncome 插入新余额收入]
     * @author Ethan
     * @date   2017-04-21
     * @param  [type]     $data [description]
     */
    public function addSpend($data){
    	$this->member_id = $data['member_id'];
    	$this->operator_type = $data['operator_type'];
    	$this->channel_id = $data['channel_id'];
    	$this->operator_id = $data['operator_id'];
    	$this->change_balance = $data['change_balance'];
    	$this->before_balance = $data['before_balance'];
    	$this->after_balance = $data['after_balance'];
    	$this->active_member = $data['active_member'];
        $this->type = $data['type'];
    	$this->relation_id = $data['relation_id'];
    	$result = $this->save();
    	if($result){
    		return true;
    	}
    }
    /**
     * [postArrIncome 余额收入传递数据]
     * @author Ethan
     * @date   2017-04-21
     * @param  [type]     $request [description]
     * @return [type]              [description]
     */
    public function postArrSpend($request,$errorlog){
        $header = $request->headers->all();
        $param = true;

        if(isset($header['member-id'])){
            $arr['member_id'] = $header['member-id'][0];
        }else{
            $errorlog->addError("less member-id from ".$request->path(),$request->headers->all());//缺少member_id
            $param = false;
        }

        if(isset($header['operator-type'])){
            $arr['operator_type'] = $header['operator-type'][0];
        }else{
            $errorlog->addError("less operator-type from ".$request->path(),$request->headers->all());//operator-type
             $param = false;
        }

        if(isset($header['channel-id'])){
            $arr['channel_id'] = $header['channel-id'][0];
        }else{
            $errorlog->addError("less channel-id from ".$request->path(),$request->headers->all());
            $param = false;
        }

        if(isset($header['operator-id'])){
            $arr['operator_id'] = $header['operator-id'][0];
        }else{
            $errorlog->addError("less operator-id from ".$request->path(),$request->headers->all());
            $param = false;
        }

        if(isset($header['change-balance'])){
            $arr['change_balance'] = $header['change-balance'][0];
        }else{
            $errorlog->addError("less change-balance from ".$request->path(),$request->headers->all());
            $param = false;
        }


        if(isset($header['active-member'])){
            $arr['active_member'] = $header['active-member'][0];
        }else{
            $errorlog->addError("less left-balance from ".$request->path(),$request->headers->all());
            $param = false;
        }

        if(isset($header['type'])){
            $arr['type'] = $header['type'][0];
        }else{
            $errorlog->addError("less type from ".$request->path(),$request->headers->all());
            $param = false;
        }

        if(isset($header['relation-id'])){
            $arr['relation_id'] = $header['relation-id'][0];
        }else{
            $errorlog->addError("less relation-id from ".$request->path(),$request->headers->all());
            $param = false;
        }
        if(!$param){
            unset($arr);
            return false;
        }
        return $arr;
    }
}
