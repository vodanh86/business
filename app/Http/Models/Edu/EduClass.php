<?php

namespace App\Http\Models\Edu;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use Illuminate\Database\Eloquent\Model;

class EduClass extends Model
{
    protected $table = 'edu_class';
    protected $hidden = [
    ];

	protected $guarded = [];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function getScheduleClassAttribute($value)
    {
        return explode(',', $value);
    }

    public function setScheduleClassAttribute($value)
    {
        $this->attributes['schedule'] = implode(',', $value);
    }
	
}
