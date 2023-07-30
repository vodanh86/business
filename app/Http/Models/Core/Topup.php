<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $table = 'core_topup';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
