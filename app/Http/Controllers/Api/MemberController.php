<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ErrorCodeController as error;
use App\model\MemberModel;

class MemberController extends BaseController
{
	private $model;
	public function __construct(Request $request){
        parent::__construct($request);
        $this->model = new MemberModel();
	}
    /**
     * [__destruct 析构函数]
     * @author Ethan
     * @date   2017-04-21
     */
    public function __destruct(){
        unset($model);
    }
}
