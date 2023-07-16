<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Edu\EduClass;

class ClassController extends Controller
{
    public function find(Request $request)
    {
        $branchId = $request->get('branch_id');
        $class = EduClass::where('branch_id', $branchId)->get();
        return $class;
    }
}
