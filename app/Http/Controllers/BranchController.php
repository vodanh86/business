<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Core\Branch;

class BranchController extends Controller
{
    public function find(Request $request)
    {
        $businessId = $request->get('business_id');
        $branches = Branch::where('business_id', $businessId)->get();
        return $branches;
    }
}
