<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Edu\EduStudent;

class StudentController extends Controller
{
    public function find(Request $request)
    {
        $scheduleId = $request->get('schedule_id');
        $schedule = EduStudent::where('schedule_id', $scheduleId)->get();
        return $schedule;
    }
}
