<?php

namespace App\Http\Models\Edu;

use Illuminate\Database\Eloquent\Model;

class EduEmployee extends Model
{
    protected $table = 'edu_employee';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function company()
    {
        return $this->belongsTo(Branch::class, 'company_id');
    }


	protected $hidden = [
    ];

	protected $guarded = [];
}
