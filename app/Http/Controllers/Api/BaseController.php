<?php

namespace App\Http\Controllers\Api;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class BaseController extends Controller
{
	public $errorlog;
	/**
	 * [__construct 构造函数]
	 * @author Ethan
	 * @date   2017-04-21
	 * @param  Request    $request [description]
	 */
	public function __construct(Request $request){
		//错误日志
        $logpath = storage_path("member/".date("Y-m-d")."membererror.log");
        $this->errorlog = new Logger("member");
        $this->errorlog->pushHandler(new StreamHandler($logpath,Logger::INFO));
	}

    /**
     * [__destruct 析构函数]
     * @author Ethan
     * @date   2017-04-21
     */
    public function __destruct(){
        unset($errorlog,$incomeModel,$spendModel);
    }
}
