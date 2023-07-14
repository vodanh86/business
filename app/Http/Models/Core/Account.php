<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'core_account';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
