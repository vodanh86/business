<?php

namespace App\Http\Models\Edu;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use Illuminate\Database\Eloquent\Model;

class EduApplyLeave extends Model
{
    protected $table = 'edu_apply_leave';
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
    public function schedule()
    {
        return $this->belongsTo(EduSchedule::class, 'schedule_id');
    }
   
}
