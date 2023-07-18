<?php

namespace App\Http\Controllers;

use App\Http\Models\Edu\EduSchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function find(Request $request)
    {
        $branchId = $request->get('branch_id');
        $schedule = EduSchedule::where('branch_id', $branchId)->get();
        return $schedule;
    }
    public function getById(Request $request)
    {
        $id = $request->get('q');
        $schedule = EduSchedule::find($id);
        return $schedule;
    }
}
