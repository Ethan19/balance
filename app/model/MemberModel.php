<?php
namespace App\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\model\IncomeModel;
use App\model\SpendModel;
use App\model\LogModel;
use App\model\IncomeSpendModel;
use App\Http\Controllers\ErrorCodeController as error;

class MemberModel extends Model
{
	protected $table = 'member';
	protected $primaryKey = "member_id";

    public function save(array $options=[]){
        $options[0]['before_balance'] = $options[1];
        $options[0]['after_balance'] = $options[2];
        LogModel::addLog($options[0]);
        return parent::save();
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
