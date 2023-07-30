<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Core\Business;

class BusinessController extends Controller
{
    public function find(Request $request)
    {
        $id = $request->get('q');
        $biz = Business::find($id);
        return $biz;
    }
}
