<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Models\AdminUser;
use App\Http\Models\Core\Branch;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;

class HomeController extends Controller
{
    public function index(Content $content)
    {   
        $userCount = AdminUser::where('business_id', '=', Admin::user()->business_id)->count();
        $branchCount = Branch::where('business_id', '=', Admin::user()->business_id)->count();

        return $content
        ->header('Trang chủ')
        ->body(new Box('Tổng quan', view('admin.dashboard', compact('userCount', 'branchCount'))));
    }
}
