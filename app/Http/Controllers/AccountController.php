<?php

namespace App\Http\Controllers;

use App\Http\Models\Core\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function find(Request $request)
    {
        $id = $request->get('q');
        $account = Account::find($id);
        return $account;
    }
}
