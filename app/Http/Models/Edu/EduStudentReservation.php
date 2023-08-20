<?php

namespace App\Http\Models\Edu;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use Illuminate\Database\Eloquent\Model;

class EduStudentReservation extends Model
{
    protected $table = 'edu_student_reservation';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    } public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function student()
    {
        return $this->belongsTo(EduStudent::class, 'student_id');
    }
    public function schedule()
    {
        return $this->belongsTo(EduSchedule::class, 'schedule_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
