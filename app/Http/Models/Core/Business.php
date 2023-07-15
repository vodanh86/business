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
    public function businessType()
    {
        return $this->belongsTo(Business_Type::class, 'type');
    }
    protected $hidden = [];

    protected $guarded = [];
}




