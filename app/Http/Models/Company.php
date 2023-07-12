<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function companies()
    {
        return $this->hasMany(Company::class, 'company_id');
    }
    protected $hidden = [
    ];

    protected $guarded = [];
}
