<?php
namespace App\Http\Support;

use App\model\SpendModel;
use App\model\MemberModel;
/**
 * Spend辅助类
 */
class SpendSupport{

	public function __construct(){

	}
	public function getSpendList($request){
		$page = isset($request->query()['page'])?$request->query()['page']:1; 
		$model = new SpendModel();
		$pageModel = SpendModel::with(array('member','channel','operation'))->paginate(10,array('*'),'page',$page);
		$res = $pageModel->toArray();
		for ($i=0; $i <count($res['data']) ; $i++) { 
			$res['data'][$i]['nickname'] = $res['data'][$i]['member']['nickname'];
			$res['data'][$i]['channelname'] = $res['data'][$i]['channel']['name'];
			$res['data'][$i]['operationname'] = $res['data'][$i]['operation']['name'];
			unset($res['data'][$i]['member'],$res['data'][$i]['channel'],$res['data'][$i]['operation']);
		}
		$result = array('list'=>$res['data'],'total_num'=>$res['total'],'current_page'=>$res['current_page'],'total_page'=>$res['last_page'],"per_page"=>$res['per_page'],"url"=>$pageModel->resolveCurrentPath(),"pageName"=>$pageModel->getPageName());
		return response()->json($result);
	}
}










?>