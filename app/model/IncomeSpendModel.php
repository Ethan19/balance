<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class IncomeSpendModel extends Model
{
	protected $table = 'income_spend';
    protected $fillable = array('income_id','spend_id','change_balance','rollback_balance');
    
}
