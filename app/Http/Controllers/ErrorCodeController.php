<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class ErrorCodeController extends Controller
{
    const ERROR_CODE_MSG_LESS_PARAMS = "LESS PARAMS";
    const ERROR_CODE_CODE_LESS_PARAMS = 10001;

    const ERROR_CODE_MSG_CREATE_FAIL = "ADD DATABASE FAIL";
    const ERROR_CODE_CODE_CREATE_FAIL = 10002;

    const ERROR_CODE_MSG_LESS_BALANCE = "NOT ENOUGH BALANCE";
    const ERROR_CODE_CODE_LESS_BALANCE = 10003;

    const ERROR_MSG_INSERT_SPEND_FAIL = "INSERT SPEND FAIL";
    const ERROR_CODE_INSERT_SPEND_FAIL = 10004;

    const ERROR_MSG_INSERT_INCOME_FAIL = "INSERT INCOME FAIL";
    const ERROR_CODE_INSERT_INCOME_FAIL = 10005;

    const ERROR_MSG_INSERT_SP_FAIL = "INSERT INCOMESPEND FAIL";
    const ERROR_CODE_INSERT_SP_FAIL = 10006;

    const ERROR_MSG_UPDATE_INCOME_FAIL = "UPDATE INCOME FAIL";
    const ERROR_CODE_UPDATE_INCOME_FAIL = 10007;

    const ERROR_MSG_UPDATE_MEMBER_FAIL = "UPDATE MEMBER FAIL";
    const ERROR_CODE_UPDATE_MEMBER_FAIL = 10008;

    const ERROR_MSG_ROLLBACKBALANCE_MISTAKE = "ROLLBACL DATA MISTAKE";
    const ERROR_CODE_ROLLBACKBALANCE_MISTAKE = 10009;

    const ERROR_MSG_UPDATE_ROLLBACK_FAIL = "UPDATE ROLLBACK FAIL";
    const ERROR_CODE_UPDATE_ROLLBACK_FAIL = 10010;

    const ERROR_MSG_UNCORRECT_MEMBER = "UNCORRECT MEMBER_ID";
    const ERROR_CODE_UNCORRECT_MEMBER = 10011;

    const ERROR_MSG_INSERT_RI_FAIL = "INSERT ROLLBACKINCOME FAIL";
    const ERROR_CODE_INSERT_RI_FAIL = 10012;

    const SUCCESS_CODE_MSG_CREATE_OK = "ADD INCOME SUCCESS";
    const SUCCESS_CODE_CODE_CREATE_OK = 20001;

    const SUCCESS_MSG_OK = "SUCCESS";
    const SUCCESS_CODE_OK = 20002;

    public static function sendJsonFailMsg($msg,$code){
    	return  response()->json(array("status"=>"fail","message"=>$msg,"code"=>$code));
    }

    public static function sendJsonSuccessMsg($msg,$code){
    	return  response()->json(array("status"=>"success","message"=>$msg,"code"=>$code));
    }
}