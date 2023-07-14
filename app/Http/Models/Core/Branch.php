<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'core_branch';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function classes()
    {
        return $this->hasMany(EduClass::class,'branch_code');
    }
    protected $hidden = [
    ];

    protected $guarded = [];
}
