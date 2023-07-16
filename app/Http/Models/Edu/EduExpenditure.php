<?php

namespace App\Http\Models\Edu;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\Expense;
use Illuminate\Database\Eloquent\Model;

class EduExpenditure extends Model
{
    protected $table = 'edu_expenditure';
    protected $hidden = [
    ];

	protected $guarded = [];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function class()
    {
        return $this->belongsTo(EduClass::class, 'class_id');
    }
    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
   
}
