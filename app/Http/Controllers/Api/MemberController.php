<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    //
    public function addIncome(Request $request){
    	$data = $request->headers->all();
    	if($data['memberid']){
    		$arr['member_id'] = $data['memberid'][0];
    	}else{
    		log::info("heloow");	
    	}
    }
}
