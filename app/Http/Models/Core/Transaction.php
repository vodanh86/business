<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'core_transaction';
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
     public function txnType()
    {
        return $this->belongsTo(TxnTypeCondition::class, 'txn_type_id');
    }
    protected $hidden = [];

    protected $guarded = [];
}




