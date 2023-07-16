<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TransactionCode extends Model
{
    protected $table = 'core_transaction_code';
    public function creditTransaction()
    {
        return $this->belongsTo(CoreTransactionCode::class, 'txn_code_credit');
    }

    public function debitTransaction()
    {
        return $this->belongsTo(CoreTransactionCode::class, 'txn_code_debit');
    }

    public function chargeTransaction()
    {
        return $this->belongsTo(CoreTransactionCode::class, 'txn_code_charge');
    }
    protected $hidden = [];

    protected $guarded = [];
}




