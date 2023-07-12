<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }


	protected $hidden = [
    ];

	protected $guarded = [];
}
