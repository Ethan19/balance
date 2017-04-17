<?php

namespace App\Http\Middleware;

use Closure;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class ApiAuthVerify
{
    private $infolog;

    public function __construct(){

        $logpath = storage_path("middlelog/".date("Y-m-d")."info.log");
        $this->infolog = new Logger("info");
        $this->infolog->pushHandler(new StreamHandler($logpath,Logger::INFO));
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
        $uri = $request->path();//当前请求路劲，不包含header
        $header = $request->headers->all();
        $this->infolog->addInfo("receive date from ".$uri,$header);//写日志
        // info("receive date from ".$uri,$header);
        // echo _METHOD_;die;
        // $data = get_defined_constants();

        // $this->infolog()->addRecord("receive data",array());
        // dump($data);
        // foreach ($data as $key => $value) {
        //     echo $value."\n";
        // }
        // echo "<pre>"; 
        // var_dump(get_defined_constants());
        // echo "</pre>";
        // die;
        // $data = $request->headers->all();
        // echo "<pre>"; 
        // var_dump($data);
        // echo "</pre>";
        // die;
        // echo "<pre>"; 
        // var_dump($request->all());
        // echo "</pre>";
        // die;
        // echo "<pre>"; 
        // var_dump($data);
        // echo "</pre>";
        // die;
        //在此处写过滤代码
        return $next($request);
    }
}
