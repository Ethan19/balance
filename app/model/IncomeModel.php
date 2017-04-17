<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class IncomeModel extends Model
{
    //
	protected $table = 'income';
	protected $primaryKey = "income_id";

    public function addIncome($data){
    	$this->member_id = $data['member_id'];
    	$this->operator_type = $data['operator_type'];
    	$this->channel_id = $data['channel_id'];
    	$this->operator_id = $data['operator_id'];
    	$this->change_balance = $data['change_balance'];
    	$this->before_balance = $data['before_balance'];
    	$this->after_balance = $data['after_balance'];
    	$this->left_balance = $data['left_balance'];
    	$this->active_member = $data['active_member'];
    	$this->type = $data['type'];
    	$result = $this->save();
    	if($result){
    		return true;
    	}
    }
}
