<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\ErrorCodeController as error;

use App\model\IncomeModel;
use App\model\MemberModel;

use App\Http\Support\IncomeSupport;

/**
 * incomebalance=balance+spendbalance-rollbackbalance+recoverbalance
 */
class IncomeController extends BaseController
{
	private $incomeModel;
	public function __construct(Request $request){
		parent::__construct($request);
        $this->incomeModel = new IncomeModel();
        $this->memberModel = new MemberModel();
	}
    public function Index(Request $request){
        $incomeSupport = new IncomeSupport();
        $incomeSupport->getIncomeList();
    }
    /**
     * [addIncome 插入余额收入]
     * @author Ethan
     * @date   2017-04-17
     * @param  Request    $request [description]
     */
    public function addIncome(Request $request){
    	$data = $this->postArrIncome($request);

        if(!$data){
            return error::sendJsonFailMsg(error::ERROR_CODE_MSG_LESS_PARAMS,error::ERROR_CODE_CODE_LESS_PARAMS);
        }
        //增加用户收入余额和写进收入余额
        $result = $this->incomeBalance($data);
        if($result){
            return error::sendJsonSuccessMsg(error::SUCCESS_CODE_MSG_CREATE_OK,error::SUCCESS_CODE_CODE_CREATE_OK);
        }else{
            return error::sendJsonFailMsg(error::ERROR_CODE_MSG_CREATE_FAIL,error::ERROR_CODE_CODE_CREATE_FAIL);
        }

    }
    /**
     * [changeBalance 增加账户收入余额，加法]
     * @author Ethan
     * @date   2017-04-21
     * @param  [type]     $data [description]
     * @return [type]           [description]
     */
    public function incomeBalance($data){       
        
        $model = $this->memberModel->getMember($data['member_id']);
        //组织income表数据
        $data['before_balance'] = $model->balance;//变动前的可以使用余额
        $data['after_balance'] = $model->balance+$data['change_balance'];//变动后的可以使用余额
        $data['left_balance'] = $data['change_balance'];//剩余可使用余额
        //修改member表数据 收入余额incomebalance增加，总余额balance增加，其他余额不变
        $model->incomebalance += $data['change_balance'];//收入余额
        $model->balance += $data['change_balance'];
        //使用事务
        DB::beginTransaction();
        $mRes = $model->save(array($data,$data['before_balance'],$data['after_balance']));
        if(!$mRes){
            DB::rollBack();
            return false;
        }
        $iRes = $this->incomeModel->create($data);
        if(!$iRes){
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;


    }
    /**
     * [postArrIncome 余额收入传递数据]
     * @author Ethan
     * @date   2017-04-21
     * @param  [type]     $request [description]
     * @return [type]              [description]
     */
    public function postArrIncome($request){
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
        if(!$param){
            unset($arr);
            return false;
        }
        return $arr;
    }
    public function __destruct(){
    	unset($incomeModel);
    }
}
