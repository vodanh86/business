<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'core_expense';
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function groupExpense()
    {
        return $this->belongsTo(ExpenseGroup::class, 'group_id');
    }
    public function typeExpense()
    {
        return $this->belongsTo(ExpenseType::class, 'type_id');
    }
    protected $hidden = [];

    protected $guarded = [];
}




