<?php

namespace App\Http\Middleware;

use Closure;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\model\MemberModel;

use App\Http\Controllers\ErrorCodeController as error;

class ApiAuthVerify
{
    public function __construct(Request $request){

        $logpath = storage_path("middlelog/".date("Y-m-d")."info.log");
        $infolog = new Logger('middle');
        $infolog->pushHandler(new StreamHandler($logpath,Logger::INFO));
        $infolog->addInfo("receive date from ".$request->path(),[]);//写日志
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header = $request->headers->all();
        if(!isset($header['appkey'])){
            return error::sendJsonFailMsg(error::ERROR_MSG_APPKEYTOKEN_UNCORRECT,error::ERROR_CODE_APPKEYTOKEN_UNCORRECT);
        }else{
            $appkey = $header['appkey'][0];
        }

        if(!isset($header['token'])){
            return error::sendJsonFailMsg(error::ERROR_MSG_APPKEYTOKEN_UNCORRECT,error::ERROR_CODE_APPKEYTOKEN_UNCORRECT);
        }else{
            $token = $header['token'][0];
        }
         //加密规则
         //0.balanceChannel的key值
         //1.member_id
         //2.时间戳
        //通过key查找channelsecret
         //appkey = base64_encode(key,memberid,time())
         //token = hash_hmac("md5", $memberid, channelsecret);
         
        $basetoken = explode("|",base64_decode($appkey));
        //校验解密后的参数是否合法
        if(count($basetoken)!=3){
            return error::sendJsonFailMsg(error::ERROR_MSG_APPKEYTOKEN_UNCORRECT,error::ERROR_CODE_APPKEYTOKEN_UNCORRECT);
        }
        $memberinfo = MemberModel::find($basetoken[1]);
        //校验用户是否存在
        if(!$memberinfo){
            return error::sendJsonFailMsg(error::ERROR_MSG_APPKEYTOKEN_UNCORRECT,error::ERROR_CODE_APPKEYTOKEN_UNCORRECT);
        }
        $balanceInfo = Config::get('api.balanceChannel');
        //校验秘钥是否存在
        $secret = $balanceInfo[$basetoken[0]]['channelsecret'];
        if(!$secret){
            return error::sendJsonFailMsg(error::ERROR_MSG_APPKEYTOKEN_UNCORRECT,error::ERROR_CODE_APPKEYTOKEN_UNCORRECT);  
        }
        //校验秘钥
        if(hash_hmac("md5", $basetoken[1], $secret) != $token){
            return error::sendJsonFailMsg(error::ERROR_MSG_APPKEYTOKEN_UNCORRECT,error::ERROR_CODE_APPKEYTOKEN_UNCORRECT); 
        }
        //验证通过
        
        return $next($request);
    }
}
