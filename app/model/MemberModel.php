<?php
namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\model\IncomeModel;
use App\model\SpendModel;
use App\model\IncomeSpendModel;
use App\Http\Controllers\ErrorCodeController as error;

class MemberModel extends Model
{
	protected $table = 'member';
	protected $primaryKey = "member_id";
	private $incomeModel;
	public function __construct(){
		$this->incomeModel = new IncomeModel();
	}
    

    public function getMember($member_id){
    	$obj = MemberModel::find($member_id);
    	return $obj;
    }

    public function setMember($obj,$data){
    	$obj->balance =$obj->balance - $data['change_balance'];
    	$obj->spendbalance +=$data['change_balance'];
    	$uRes = $obj->save();
    	return $uRes;
    }
    /**
     * [rollbackBalance incomebalance增加，rollbackbalance减少]
     * @author Ethan
     * @date   2017-04-21
     * @return [type]     [description]
     */
    public function rollbackBalance(){

    }
    /**
     * [recoveryBalance incomebalance减少，recoverybalance增加]
     * @author Ethan
     * @date   2017-04-21
     * @return [type]     [description]
     */
    public function recoveryBalance(){

    }
}
