<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Edu\EduStudent;

class StudentController extends Controller
{
    public function find(Request $request)
    {
        $classId = $request->get('class_id');
        $class = EduStudent::where('class_id', $classId)->get();
        return $class;
    }
}
