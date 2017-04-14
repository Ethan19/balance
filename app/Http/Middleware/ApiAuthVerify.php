<?php

namespace App\Http\Middleware;

use Closure;
use Monolog\Logger;
use Monolog\Handle\StreamHandler;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class ApiAuthVerify
{
    // private $log;

    public function __destruct(){
        // $this->log = new Logger();
        // $this->log->pushHandler(new StreamHandler(""),Logger::WARNING);
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
        $data = get_defined_constants();
        dump($data);
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
