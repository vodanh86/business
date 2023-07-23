<?php

namespace App\Http\Models\Edu;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use Illuminate\Database\Eloquent\Model;

class EduStudentReportDetail extends Model
{
    protected $table = 'edu_report_detail';

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
    public function studentReport()
    {
        return $this->belongsTo(EduStudentReport::class, 'student_report_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
