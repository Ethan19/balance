<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class ErrorCodeController extends Controller
{
    const ERROR_CODE_MSG_LESS_PARAMS = "缺少参数";
    const ERROR_CODE_MSG_CODE = 10001;

    public function sendJsonMsg($status,$msg,$code){
    	return response()->json(array("success"=>$status,"message"=>$msg,"code"=>$code));
    }
}
