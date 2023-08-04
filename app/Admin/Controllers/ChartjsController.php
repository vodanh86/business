<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Models\Edu\EduStudent;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\DB;

class ChartjsController extends Controller
{
    public function index(Content $content)
    {
        $result = DB::select("
        SELECT 
            esc.name, count(est.id) count_student 
        FROM 
            business.edu_student est 
        INNER JOIN  
            business.edu_schedule esc 
        ON 
            est.schedule_id = esc.id
        WHERE 
            est.business_id = ?
        GROUP BY 
            esc.name;
        ",[Admin::user()->business_id]);

        $labels = [];
        $data = [];
        foreach ($result as $label) {
            
            $labels[] = $label->name;
            $data[] = $label->count_student;
        }
        $chartLineData = [
            'labels' => $labels,
            'data' => $data
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
