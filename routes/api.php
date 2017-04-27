<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/',function(){
    echo "NOT FOUND!";die;
});
Route::group(['middleware' => ['api.auth.verify']], function () {
    Route::get("test","TestController@Test");//测试
    // Route::get("addincome","App\Http\Controllers\Api\MemberController@addIncome");//
    Route::post("addincome","Api\IncomeController@addIncome");//
    Route::post("addspend","Api\SpendController@addspend");//
    Route::post("addrollbackbyspendid","Api\RollbackController@addRollbackBySpendId");//
    Route::post("addrecovery","Api\RecoveryController@addRecovery");//
    //一下为列表也
    Route::get("incomelist","Api\IncomeController@Index");//
    Route::get("spendlist","Api\SpendController@Index");//
    Route::get("rollbacklist","Api\RollbackController@Index");//
    Route::get("recoverylist","Api\RecoveryController@Index");//
    // Route::get("addincome",function(){
    // 	echo 2333333;
    // });
});
