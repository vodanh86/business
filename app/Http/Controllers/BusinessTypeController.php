<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Core\Business_Type;

class BusinessTypeController extends Controller
{
    public function find(Request $request)
    {
        $id = $request->get('q');
        $contract = Business_Type::find($id);
        return $contract;
    }
}
