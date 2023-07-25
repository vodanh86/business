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
    public function getById(Request $request)
    {
        $id = $request->get('q');
        $class = EduClass::find($id);
        return $class;
    }
    public function getAll(Request $request)
    {
        $classes = EduClass::all();
        return $classes;
    }
}
