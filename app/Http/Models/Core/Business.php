<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'core_business';

    public function branchs()
    {
        return $this->hasMany(Branch::class);
    }
    public function account()
    {
        return $this->hasMany(Account::class);
    }
    protected $hidden = [];

    protected $guarded = [];
}




