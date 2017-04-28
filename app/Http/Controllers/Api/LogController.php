<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Support\LogSupport;
class LogController extends BaseController
{
    public function __construct(Request $request){
		parent::__construct($request);
	}
    public function Index(Request $request){
        $logSupport = new LogSupport();
        $result = $logSupport->getLogList($request);
        return $result;
    }
}
