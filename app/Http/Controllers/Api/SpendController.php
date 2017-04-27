<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\ErrorCodeController as error;
use App\model\MemberModel;
use App\model\IncomeModel;
use App\model\SpendModel;
use App\model\IncomeSpendModel;

use App\Http\Support\SpendSupport;

class SpendController extends BaseController
{
	public function __construct(Request $request){
		parent::__construct($request);
        $this->spendModel = new SpendModel();
		$this->MemberModel = new MemberModel();

		
	}
    /**
     * [Index 消费列表]
     * @author Ethan
     * @date   2017-04-27
     * @param  Request    $request [description]
     */
    public function Index(Request $request){
        $spendSupport = new SpendSupport();
        $result = $spendSupport->getSpendList($request);
        return $result;
    }


    /**
     * [addSpend 增加支出]
     * @author Ethan
     * @date   2017-04-24
     * @param  Request    $request [description]
     */
    public function addSpend(Request $request){
        $data = $this->spendModel->postArrSpend($request,$this->errorlog);
        if(!$data){
            return error::sendJsonFailMsg(error::ERROR_CODE_MSG_LESS_PARAMS,error::ERROR_CODE_CODE_LESS_PARAMS);
        }
        $result = $this->spendBalance($data);
        return $result;
    }


    /**
     * [spendBalance 增加账户消费余额，incomebalance减少，spendbalance增加]
     * @author Ethan
     * @date   2017-04-21
     * @return [type]     [description]
     */
    public function spendBalance(Array $data){
        $member = MemberModel::find($data['member_id']);
        if($data['change_balance']>$member->balance){
            return error::sendJsonFailMsg(error::ERROR_CODE_MSG_LESS_BALANCE,error::ERROR_CODE_CODE_LESS_BALANCE);//余额不足
        }
        //开始事务
        $res = $this->getIncomeReduce($data);
        return $res;



    }
    /**
     * [getIncomeReduce 支出收入分配方式]
     * @author Ethan
     * @date   2017-04-24
     * @param  [type]     $data [spend数组]
     * @return [type]           [description]
     */
    private function getIncomeReduce($data){
        //获取用户现有的所有数据
        $userInfo = $this->MemberModel->getMember($data['member_id']);
        // if(empty($userInfo['member_id'])){
        //  return 1;//用户不存在
        // }
        $data['before_balance'] = $userInfo['balance'];
        $data['after_balance'] = $userInfo['balance']-$data['change_balance'];
        //新增数据进spend表
        DB::beginTransaction();
        $sRes = SpendModel::create($data);
        if(!$sRes->spend_id){
            DB::rollBack();
            return 2;//写spend表失败
        }
        $spend_id = $sRes->spend_id;
        //逐项扣减income表
        $balance = $data['change_balance'];
        while ( (float)$balance > 0) {
            $incomeInfo = IncomeModel::whereRaw("operator_type=1 and left_balance>0 and member_id=".$data['member_id'])->orderBy('income_id','asc')->first();
                //增加balance_income_spend数据
                $isData['income_id'] = $incomeInfo['income_id'];
                $isData['spend_id'] = $spend_id;
            if($balance>=$incomeInfo['left_balance']){
                $isData['change_balance'] = $incomeInfo['left_balance'];
                $isData['rollback_balance'] = $incomeInfo['left_balance'];
                $isRes = IncomeSpendModel::create($isData);
                if(!$isRes->id){
                    DB::rollBack();
                    return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_SPEND_FAIL,error::ERROR_CODE_INSERT_SPEND_FAIL);
                    //写进income_spend表失败
                }

                $incomeModel = IncomeModel::find($incomeInfo['income_id']);
                $incomeModel->left_balance = 0;
                $iRes = $incomeModel->save();//返回true or false
                if(!$iRes){
                    DB::rollBack();
                    return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_INCOME_FAIL,error::ERROR_CODE_INSERT_INCOME_FAIL);
                    //修改income表失敗
                }
                $balance = $balance-$incomeInfo['left_balance'];

            }elseif($balance<$incomeInfo['left_balance']){
                $isData['change_balance'] = $balance;
                $isData['rollback_balance'] = $balance;
                $isRes = IncomeSpendModel::create($isData);
                if(!$isRes->id){
                    DB::rollBack();
                    return error::sendJsonFailMsg(error::ERROR_MSG_INSERT_SP_FAIL,error::ERROR_CODE_INSERT_SP_FAIL);
                    //写进income_spend表失败
                }
                $incomeModel = IncomeModel::find($incomeInfo['income_id']);
                $incomeModel->left_balance = $incomeInfo['left_balance']-$balance;
                $iRes = $incomeModel->save();
                if(!$iRes){
                    DB::rollBack();
                    return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_INCOME_FAIL,error::ERROR_CODE_UPDATE_INCOME_FAIL);
                    //修改income表失敗
                }
                $balance = 0;
            }
        }
        //修改用户信息的可用余额\
        $userInfo->balance = $userInfo->balance-$data['change_balance'];
        $userInfo->spendbalance += $data['change_balance'];
        $uRes = $userInfo->save(array($data,$userInfo->balance+$data['change_balance'],$userInfo->balance));
        if(!$uRes){
            DB::rollBack();
            return error::sendJsonFailMsg(error::ERROR_MSG_UPDATE_MEMBER_FAIL,error::ERROR_CODE_UPDATE_MEMBER_FAIL);
        }
        DB::commit();
        return error::sendJsonSuccessMsg(error::SUCCESS_MSG_OK,error::SUCCESS_CODE_OK);
    }
}
