<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'core_transfer';
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function creditAccount()
    {
        return $this->belongsTo(Account::class, 'credit_acct_id');
    }  
    public function debitAccount()
    {
        return $this->belongsTo(Account::class, 'debit_acct_id');
    }  
    protected $hidden = [];

    protected $guarded = [];
}




