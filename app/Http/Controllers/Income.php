<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Income;

class Income extends Controller
{
    //
    //
    public function index(){
    	$res = Income::firstOrCreate ();
    		// ->orderBy("income_id","desc")
    		// ->take(10)
    		// ->get();
    }
}
