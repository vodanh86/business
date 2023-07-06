<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAccount extends Model
{
    protected $table = 'account';

    public function business()
    {
        return $this->hasMany(Business::class);
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
