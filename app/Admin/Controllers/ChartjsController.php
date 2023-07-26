<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;

class ChartjsController extends Controller
{
    public function index(Content $content)
    {
        $chartLineData = [
            'labels' => ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            'data' => [12, 19, 3, 5, 2, 3],
        ];

        $chartPieData = [
            'labels' => ['Lớp học', 'Học sinh', 'Lịch học', 'Giảng viên', 'Nhân sự', 'Đăng ký nghỉ', "Báo cáo học sinh"],
            'data' => [12, 19, 3, 5, 2, 3, 10],
        ];

        $urlClass = "http://127.0.0.1:8000/api/classes";
        $script = <<<EOT
        
        $(function() {
            var data = [];
            var classLength = {}
            $.get("$urlClass", function (classes) {
                var classesActive = classes.filter(function (cls) {
                    return cls.status === 1;
                });  
                classLength = classesActive.length
                data.push(classLength);
            });
        });
        
        EOT;
        Admin::script($script);

        return $content
            ->header('Thống kê')
            ->body(new Box('Biểu đồ thống kê', view('admin.chartjs', compact('chartLineData', 'chartPieData'))));
    }
}
