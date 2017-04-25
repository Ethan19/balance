<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class IncomeModel extends Model
{
    //
	protected $table = 'income';
	protected $primaryKey = "income_id";

    /**
     * [addIncome 插入新余额收入]
     * @author Ethan
     * @date   2017-04-21
     * @param  [type]     $data [description]
     */
    public function addIncome($data){
    	$this->member_id = $data['member_id'];
    	$this->operator_type = $data['operator_type'];
    	$this->channel_id = $data['channel_id'];
    	$this->operator_id = $data['operator_id'];
    	$this->change_balance = $data['change_balance'];
    	$this->before_balance = $data['before_balance'];
    	$this->after_balance = $data['after_balance'];
    	$this->left_balance = $data['left_balance'];
    	$this->active_member = $data['active_member'];
        $this->type = $data['type'];
    	$this->relation_id = $data['relation_id'];
    	$result = $this->save();
    	if($result){
    		return true;
    	}else{
            return false;
        }
    }
    /**
     * [postArrIncome 余额收入传递数据]
     * @author Ethan
     * @date   2017-04-21
     * @param  [type]     $request [description]
     * @return [type]              [description]
     */
    public function postArrIncome($request,$errorlog){
        $header = $request->headers->all();
        $param = true;

        if(isset($header['member-id'])){
            $arr['member_id'] = $header['member-id'][0];
        }else{
            $errorlog->addError("less member-id from ".__METHOD__);//缺少member_id
            $param = false;
        }

        if(isset($header['operator-type'])){
            $arr['operator_type'] = $header['operator-type'][0];
        }else{
            $errorlog->addError("less operator-type from ".__METHOD__);//operator-type
             $param = false;
        }

        if(isset($header['channel-id'])){
            $arr['channel_id'] = $header['channel-id'][0];
        }else{
            $errorlog->addError("less channel-id from ".__METHOD__);
            $param = false;
        }

        if(isset($header['operator-id'])){
            $arr['operator_id'] = $header['operator-id'][0];
        }else{
            $errorlog->addError("less operator-id from ".__METHOD__);
            $param = false;
        }

        if(isset($header['change-balance'])){
            $arr['change_balance'] = $header['change-balance'][0];
        }else{
            $errorlog->addError("less change-balance from ".__METHOD__);
            $param = false;
        }

        if(isset($header['active-member'])){
            $arr['active_member'] = $header['active-member'][0];
        }else{
            $errorlog->addError("less left-balance from ".__METHOD__);
            $param = false;
        }

        if(isset($header['type'])){
            $arr['type'] = $header['type'][0];
        }else{
            $errorlog->addError("less type from ".__METHOD__);
            $param = false;
        }

        if(isset($header['relation-id'])){
            $arr['relation_id'] = $header['relation-id'][0];
        }else{
            $errorlog->addError("less relation-id from ".__METHOD__);
            $param = false;
        }
        if(!$param){
            unset($arr);
            return false;
        }
        return $arr;
    }

}
