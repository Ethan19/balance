<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\model\IncomeModel;
use App\model\MemberModel;
use App\Http\Controllers\ErrorCodeController as error;

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
    /**
     * [addIncome 插入余额收入]
     * @author Ethan
     * @date   2017-04-17
     * @param  Request    $request [description]
     */
    public function addIncome(Request $request){
    	$data = $this->incomeModel->postArrIncome($request,$this->errorlog);

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
        $mRes = $model->save();
        if(!$mRes){
            DB::rollBack();
            return false;
        }
        $iRes = $this->incomeModel->addIncome($data);
        if(!$iRes){
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;


    }

    public function __destruct(){
    	unset($incomeModel);
    }
}
