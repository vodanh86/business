<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'business';

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function companies()
    {
        return $this->hasMany(Company::class, 'business_id');
    }
    protected $hidden = [];

    protected $guarded = [];
}




