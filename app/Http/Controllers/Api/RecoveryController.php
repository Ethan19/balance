<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\ErrorCodeController as error;
use App\model\IncomeModel;
use App\model\MemberModel;
use App\model\RecoveryModel;
use App\model\RecoveryIncomeModel;

class RecoveryController extends BaseController
{
    //
    public function addRecovery(Request $request){
    	$data = $this->postArrRecovery($request);
    	if(!$data){
            return error::sendJsonFailMsg(error::ERROR_CODE_MSG_LESS_PARAMS,error::ERROR_CODE_CODE_LESS_PARAMS);
        }
       	return  $this->recoveryBalance($data);
    }

    /**
     * [recoveryBalance 回收余额]
     * @author Ethan
     * @date   2017-04-25
     * @param  [type]     $data [description]
     * @return [type]           [description]
     */
    public function recoveryBalance($data){
       	//获取用户现有的所有数据
       	$member = MemberModel::find($data['member_id']);
       	if($member['balance']<$data['change_balance']){
       		return error::sendJsonFailMsg(error::ERROR_CODE_MSG_LESS_BALANCE,error::ERROR_CODE_CODE_LESS_BALANCE);
       	}
        $data['before_balance'] = $member['balance'];
        $data['after_balance'] = $member['balance']-$data['change_balance'];
        DB::beginTransaction();
        //逐项扣减income表
        $balance = $data['change_balance'];
        while ( (float)$balance > 0) {
        	$memberModel = MemberModel::find($data['member_id']);
            $incomeModel = IncomeModel::whereRaw("operator_type=1 and left_balance>0 and member_id=".$data['member_id'])->orderBy('income_id','asc')->first();

            if($balance>=$incomeModel['left_balance']){
            	$reduceBalance = $incomeModel['left_balance'];
                $balance = $balance-$incomeModel['left_balance'];
                $incomeModel->left_balance = 0;
            }elseif($balance<$incomeModel['left_balance']){

            	$reduceBalance = $balance;
                $incomeModel->left_balance = $incomeModel->left_balance-$balance;
                $balance = 0;
            }
            $arr[] = array("income_id"=>$incomeModel['income_id'],"change_balance"=>$reduceBalance);
            $incomeModel->before_balance = $memberModel->balance;
            $incomeModel->after_balance = $memberModel->balance-$reduceBalance;

            if(!$incomeModel->save()){
                DB::rollBack();
                return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_INCOME_FAIL,error::ERROR_CODE_UPDATE_INCOME_FAIL);
                //修改income表失敗
            }
            $memberModel->balance -= $reduceBalance;
            $memberModel->recoverybalance += $reduceBalance;
            // dump($memberModel);die;
            //修改用户信息的可用余额
            if(!$memberModel->save()){
            	DB::rollBack();
            	return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_MEMBER_FAIL,error::ERROR_CODE_UPDATE_MEMBER_FAIL);
            }
        }
        $recoveryModel = new RecoveryModel();
        $recModel = $recoveryModel->create($data);
        foreach ($arr as $value) {
        	$inreModel = new RecoveryIncomeModel();
        	$inreModel->income_id = $value['income_id'];
        	$inreModel->change_balance = $value['change_balance'];
        	$inreModel->recovery_id = $recModel->recovery_id;
        	if(!$inreModel->save()){
				return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_RECOVERYINCOME_FAIL,error::ERROR_CODE_INSERT_RECOVERYINCOME_FAIL);
        	}
        }
        DB::commit();
        return error::sendJsonSuccessMsg(error::SUCCESS_MSG_OK,error::SUCCESS_CODE_OK);
    }

   /**
     * [postArrIncome 回收收入传递数据]
     * @author Ethan
     * @date   2017-04-21
     * @param  [type]     $request [description]
     * @return [type]              [description]
     */
    public function postArrRecovery($request){
        $header = $request->headers->all();
        $param = true;

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

        if(isset($header['change-balance'])){
            $arr['change_balance'] = $header['change-balance'][0];
        }else{
            $this->errorlog->addError("less change-balance from ".__METHOD__);
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
            $arr['change-balance'] = $header['change-balance'][0];
        }else{
            $this->errorlog->addError("less change-balance from ".__METHOD__);
            $param = false;
        }
        if(!$param){
            unset($arr);
            return false;
        }
        return $arr;
    }
    
}
