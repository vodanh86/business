<?php

namespace App\Http\Models\Edu;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use Illuminate\Database\Eloquent\Model;

class EduSchedule extends Model
{
    protected $table = 'edu_schedule';
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
    public function teacher()
    {
        return $this->belongsTo(EduTeacher::class, 'teacher_id');
    }
    public function class()
    {
        return $this->belongsTo(EduClass::class, 'class_id');
    }
    public function getScheduleClassAttribute($value)
    {
        return explode(',', $value);
    }

    public function setScheduleClassAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['day'] = implode(',', $value);
        }
    }
	
}
