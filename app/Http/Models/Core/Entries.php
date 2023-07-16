<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Entries extends Model
{
    protected $table = 'core_entries';
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function transaction()
    {
        return $this->belongsTo(TransactionCode::class, 'transaction_code');
    }
    protected $hidden = [];

    protected $guarded = [];
}




