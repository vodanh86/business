<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Business;

class BusinessController extends Controller
{
    public function find(Request $request)
    {
        $id = $request->get('q');
        $contract = Business::find($id);
        return $contract;
    }
}
