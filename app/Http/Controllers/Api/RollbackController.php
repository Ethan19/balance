<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\ErrorCodeController as error;
use App\model\IncomeSpendModel;
use App\model\IncomeModel;
use App\model\SpendModel;
use App\model\MemberModel;
use App\model\RollbackModel;
use App\model\RollbackIncomeModel;


class RollbackController extends BaseController
{
 	/**
 	 * 回滚操作
 	 * @author Ethan
 	 * @date   2017-04-24
 	 * @param  Request    $request [description]
 	 */
    public function addRollbackBySpendId(Request $request){
    	$header = $request->headers->all();
    	$data = $this->getPostArr($header);
    	if(!$data){
            $this->errorlog->addError("less param from ".__METHOD__);
    		return error::sendJsonFailMsg(error::ERROR_CODE_MSG_LESS_PARAMS,error::ERROR_CODE_CODE_LESS_PARAMS);
    	}
        if(isset($data['change_balance'])){
            $res = $this->rollbackBySpendIdAndBalance($data);
        }else{
            $res = $this->rollbackBySpendId($data);
        }
    	
    	return $res;
    }


    /**
     * [rollbackByspendId 具体的回滚操作，根据spend_id]
     * @author Ethan
     * @date   2017-04-24
     * @param  [type]     $data [description]
     * @return [type]           [description]
     */
    public function rollbackBySpendId($data){
    	$member = MemberModel::find($data['member_id']);//用户信息
    	$before_balance = $member->balance;//用户变动前余额
    	$spendModel = SpendModel::find($data['spend_id']);
    	if($spendModel->member_id!=$data['member_id']){//用户id错误
    		return error::sendJsonFailMsg(error::ERROR_MSG_UNCORRECT_MEMBER,error::ERROR_CODE_UNCORRECT_MEMBER);
    	}
    	$change_balance = $spendModel->change_balance;//变动余额 
    	$list = IncomeSpendModel::where("spend_id","=",$data['spend_id'])->get();
    	DB::beginTransaction();//开始事务
    	foreach ($list as  $val) {//可能多条记录，逐条进行回滚
            $memberModel = MemberModel::find($data['member_id']);//用户信息
    		if((int)$val->rollback_balance == 0){//已经回滚完的数据，报错
    			DB::rollback();
    			return error::sendJsonFailMsg(error::ERROR_MSG_ROLLBACKBALANCE_MISTAKE,error::ERROR_CODE_ROLLBACKBALANCE_MISTAKE);
    		}
    		//更改income可用余额
    		$incomeModel = IncomeModel::find($val->income_id);
            $incomeModel->left_balance += $val->rollback_balance;
            $incomeModel->before_balance = $memberModel->balance;
    		$incomeModel->after_balance = $memberModel->balance+$val->rollback_balance;
    		if(!$incomeModel->save()){
    			DB::rollback();
    			return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_INCOME_FAIL,error::ERROR_CODE_UPDATE_INCOME_FAIL);
    		}
    		//更改收支表可回滚余额
    		$isModel = IncomeSpendModel::find($val->id);
    		$isModel->rollback_balance = 0;
    		$isRes = $isModel->save();
    		if(!$isRes){
    			DB::rollback();
    			return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_SP_FAIL,error::ERROR_CODE_INSERT_SP_FAIL);
    		}
    		$memberModel->balance += $val->rollback_balance;
    		$memberModel->rollbackbalance += $val->rollback_balance;
            //更改用户数据
            $mData = $data;
            unset($mData['spend_id']);
            $mData['change_balance'] = $val->rollback_balance;
            if(!$memberModel->save(array($mData,$incomeModel->before_balance,$incomeModel->after_balance))){
                DB::rollback();
                return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_MEMBER_FAIL,error::ERROR_CODE_UPDATE_MEMBER_FAIL);
            }
    	}

    	//新增rollback数据
    	$rollbackModel = new RollbackModel();
    	$rollbackModel->member_id = $spendModel->member_id;
    	$rollbackModel->operator_type = $data['operator_type'];
    	$rollbackModel->channel_id = $data['channel_id'];
    	$rollbackModel->operator_id = $data['operator_id'];
    	$rollbackModel->active_member = $data['active_member'];
    	$rollbackModel->type = $data['type'];
    	$rollbackModel->relation_id = $data['relation_id'];
    	$rollbackModel->change_balance = $spendModel->change_balance;
    	$rollbackModel->before_balance = $before_balance;
        // $rollbackModel->after_balance = $memberModel->balance;
    	$rollbackModel->after_balance = $before_balance+$spendModel->change_balance;
    	if(!$rollbackModel->save()){
			DB::rollback();
			return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_ROLLBACK_FAIL,error::ERROR_CODE_UPDATE_ROLLBACK_FAIL);
    	}
    	foreach ($list as $val) {
    		$rollIncomeModel = new RollbackIncomeModel();
    		$rollIncomeModel->rollback_id = $rollbackModel->rollback_id;//id已经存在
    		$rollIncomeModel->spend_id = $data['spend_id'];
    		$rollIncomeModel->income_id = $val->income_id;
    		$rollIncomeModel->change_balance = $val->change_balance;
    		if(!$rollIncomeModel->save()){
    			DB::rollback();
				return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_RI_FAIL,error::ERROR_CODE_INSERT_RI_FAIL);
    		}

    	}
    	DB::commit();
    	return error::sendJsonSuccessMsg(error::SUCCESS_MSG_OK,error::SUCCESS_CODE_OK);
    }

    /**
     * [rollbackBySpendIdAndBalance 指定回滚金额和spend_id]
     * @author Ethan
     * @date   2017-04-24
     * @return [type]     [description]
     */
    public function rollbackBySpendIdAndBalance($data){
        $member = MemberModel::find($data['member_id']);//用户信息
        $member_balance = $member->balance;//用户变动前余额
        $spendModel = SpendModel::find($data['spend_id']);
        if($spendModel->member_id!=$data['member_id']){//用户id错误
            return error::sendJsonFailMsg(error::ERROR_MSG_UNCORRECT_MEMBER,error::ERROR_CODE_UNCORRECT_MEMBER);
        }
        if($data['change_balance']>$spendModel->change_balance){
            $this->errorlog->addError("rollback balance uncorrect ".__METHOD__);
            return error::sendJsonFailMsg(error::ERROR_MSG_ROLLBACK_BALANCE_UNCORRECT,error::ERROR_CODE_ROLLBACK_BALANCE_UNCORRECT);
        }
        $reduce_balance = $data['change_balance'];
        DB::beginTransaction();//开始事务
        while ($reduce_balance > 0 ) {
            $memberModel = MemberModel::find($data['member_id']);//用户信息
            $before_balance = $memberModel->balance;//用户变动前余额
            $insModel = IncomeSpendModel::whereRaw(" rollback_balance>0 and spend_id=".$data['spend_id'])->orderBy('income_id','desc')->first();
            if($insModel['rollback_balance']>= $reduce_balance){
                $nowRollbackBalance = $reduce_balance;
                $insModel->rollback_balance = $insModel->rollback_balance-$reduce_balance;
                $reduce_balance = 0;
            }elseif($insModel['rollback_balance']< $reduce_balance){
                //修改IncomeSpend
                $reduce_balance = $reduce_balance-$insModel['rollback_balance'];//剩余需要回滚的金额
                $nowRollbackBalance = $insModel->rollback_balance;
                $insModel->rollback_balance = 0;//更改某条incomespend的rollback_balance值为0
            }
            $arr[] = array("income_id"=>$insModel['income_id'],"change_balance"=>$nowRollbackBalance); 
                //修改IncomeSpend
            $insRes = $insModel->save();
            if(!$insRes){
                DB::rollback();
                return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_SP_FAIL,error::ERROR_CODE_INSERT_SP_FAIL);
            }
            // 修改income表
            $incomeModel = IncomeModel::find($insModel['income_id']);
            $incomeModel->left_balance +=$nowRollbackBalance;
            $incomeModel->before_balance =$before_balance;
            $incomeModel->after_balance =$before_balance+$nowRollbackBalance;
            if(!$incomeModel->save()){
                DB::rollback();
                return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_INCOME_FAIL,error::ERROR_CODE_UPDATE_INCOME_FAIL);
            }
            //
            $mData = $data;
            unset($mData['spend_id']);
            $mData['change_balance'] = $nowRollbackBalance;
            $memberModel->balance += $nowRollbackBalance;
            $memberModel->rollbackbalance += $nowRollbackBalance;
            if(!$memberModel->save(array($mData,$incomeModel->before_balance,$incomeModel->after_balance))){
                DB::rollback();
                return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_MEMBER_FAIL,error::ERROR_CODE_UPDATE_MEMBER_FAIL);
            }
        }
        //增加rollback表
        $rollbackModel = new RollbackModel();
        $rollbackModel->member_id = $data['member_id'];
        $rollbackModel->operator_type = $data['operator_type'];
        $rollbackModel->channel_id = $data['channel_id'];
        $rollbackModel->operator_id = $data['operator_id'];
        $rollbackModel->active_member = $data['active_member'];
        $rollbackModel->type = $data['type'];
        $rollbackModel->relation_id = $data['relation_id'];
        $rollbackModel->change_balance = $data['change_balance'];
        $rollbackModel->before_balance = $member_balance;
        $rollbackModel->after_balance = $member_balance+$data['change_balance'];
        if(!$rollbackModel->save()){
            DB::rollback();
            return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_ROLLBACK_FAIL,error::ERROR_CODE_UPDATE_ROLLBACK_FAIL);
        }
        //回滚分配表
        foreach ($arr as $val) {
            $rollIncomeModel = new RollbackIncomeModel();
            $rollIncomeModel->rollback_id = $rollbackModel->rollback_id;//id已经存在
            $rollIncomeModel->spend_id = $data['spend_id'];
            $rollIncomeModel->income_id = $val['income_id'];
            $rollIncomeModel->change_balance = $val['change_balance'];
            if(!$rollIncomeModel->save()){
                DB::rollback();
                return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_RI_FAIL,error::ERROR_CODE_INSERT_RI_FAIL);
            }
        }
        DB::commit();
        return error::sendJsonSuccessMsg(error::SUCCESS_MSG_OK,error::SUCCESS_CODE_OK);

        //变动余额 
    }
    /**
     * [getPostArr header头判断]
     * @author Ethan
     * @date   2017-04-24
     * @param  [type]     $header [description]
     * @return [type]             [description]
     */
    public function getPostArr($header){
    	$param = true;
    	if(isset($header['spend-id'])){
    		$arr['spend_id'] = $header['spend-id'][0];
    	}else{
    		$this->errorlog->addError("less spend-id from ".__METHOD__);
    		$param = false;
    	}
        if(isset($header['member-id'])){
            $arr['member_id'] = $header['member-id'][0];
        }else{
            $this->errorlog->addError("less member-id from ".__METHOD__);//缺少member_id
            $param = false;
        }

        if(isset($header['operator-type'])){
            $arr['operator_type'] = $header['operator-type'][0];
        }else{
            $this->errorlog->addError("less operator-type from ".__METHOD__);//operator-type
             $param = false;
        }

        if(isset($header['channel-id'])){
            $arr['channel_id'] = $header['channel-id'][0];
        }else{
            $this->errorlog->addError("less channel-id from ".__METHOD__);
            $param = false;
        }

        if(isset($header['operator-id'])){
            $arr['operator_id'] = $header['operator-id'][0];
        }else{
            $this->errorlog->addError("less operator-id from ".__METHOD__);
            $param = false;
        }


        if(isset($header['active-member'])){
            $arr['active_member'] = $header['active-member'][0];
        }else{
            $this->errorlog->addError("less left-balance from ".__METHOD__);
            $param = false;
        }

        if(isset($header['type'])){
            $arr['type'] = $header['type'][0];
        }else{
            $this->errorlog->addError("less type from ".__METHOD__);
            $param = false;
        }

        if(isset($header['relation-id'])){
            $arr['relation_id'] = $header['relation-id'][0];
        }else{
            $this->errorlog->addError("less relation-id from ".__METHOD__);
            $param = false;
        }

        if(isset($header['change-balance'])){
            $arr['change_balance'] = $header['change-balance'][0];
        }

        if(!$param){
            unset($arr);
            return false;
        }
        return $arr;
    }
}
