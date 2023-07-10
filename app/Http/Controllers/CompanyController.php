<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Company;

class CompanyController extends Controller
{
    public function find(Request $request)
    {
        $id = $request->get('q');
        $contract = Company::find($id);
        return $contract;
    }
}
