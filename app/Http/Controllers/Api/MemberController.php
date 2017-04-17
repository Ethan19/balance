<?php

namespace App\Http\Controllers\Api;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ErrorCodeController;

use Symfony\Component\HttpFoundation\Response;



use App\model\IncomeModel;

class MemberController extends Controller
{
	private $infolog;
	public function __construct(Request $request){

		//错误日志
        $logpath = storage_path("member/".date("Y-m-d")."membererror.log");
        $this->errorlog = new Logger("member");
        $this->errorlog->pushHandler(new StreamHandler($logpath,Logger::INFO));
        $this->incomeModel = new IncomeModel();
        //写日志
		// $header = $request->headers->all();
  //   	$uri = $request->path();
  //   	$this->infolog->addInfo("receive date from ".$uri,$header);//写日志
	}
    /**
     * [addIncome 余额收入]
     * @author Ethan
     * @date   2017-04-17
     * @param  Request    $request [description]
     */
    public function addIncome(Request $request){
    	$data = $this->postArrIncome($request);
    	$result = $this->incomeModel->addIncome($data);
    	return response()->json();

    }

    private function postArrIncome($request){
    	$header = $request->headers->all();

    	if(isset($header['member-id'])){
    		$arr['member_id'] = $header['member-id'][0];
    	}else{
    		$this->errorlog->addError("less member-id from ".$request->path(),$request->headers->all());//缺少member_id
    		return ErrorCodeController()->sendJsonMsg("fail",ERROR_CODE_MSG_LESS_PARAMS,10001);
    	}

    	if(isset($header['operator-type'])){
    		$arr['operator_type'] = $header['operator-type'][0];
    	}else{
    		$this->errorlog->addError("less operator-type from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['channel-id'])){
    		$arr['channel_id'] = $header['channel-id'][0];
    	}else{
    		$this->errorlog->addError("less channel-id from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['operator-id'])){
    		$arr['operator_id'] = $header['operator-id'][0];
    	}else{
    		$this->errorlog->addError("less operator-id from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['change-balance'])){
    		$arr['change_balance'] = $header['change-balance'][0];
    	}else{
    		$this->errorlog->addError("less change-balance from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['before-balance'])){
    		$arr['before_balance'] = $header['before-balance'][0];
    	}else{
    		$this->errorlog->addError("less before-balance from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['after-balance'])){
    		$arr['after_balance'] = $header['after-balance'][0];
    	}else{
    		$this->errorlog->addError("less after-balance from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['left-balance'])){
    		$arr['left_balance'] = $header['left-balance'][0];
    	}else{
    		$this->errorlog->addError("less left-balance from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['active-member'])){
    		$arr['active_member'] = $header['active-member'][0];
    	}else{
    		$this->errorlog->addError("less left-balance from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['type'])){
    		$arr['type'] = $header['type'][0];
    	}else{
    		$this->errorlog->addError("less type from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	if(isset($header['relation_id'])){
    		$arr['relation_id'] = $header['relation_id'][0];
    	}else{
    		$this->errorlog->addError("less relation_id from ".$request->path(),$request->headers->all());//缺少member_id
    	}

    	// $arr['created_at'] = date("Y-m-d H:i:s");
    	return $arr;
    }
}
