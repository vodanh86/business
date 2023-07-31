<?php

namespace App\Admin\Controllers;

class Edu_ReportOfStudentController extends Edu_StudentReportDetailController
{
    protected $title = 'Báo cáo chi tiết học đình';

    public function index($id)
    {
        $filteredGrid = $this->filteredGrid($id);
        return $this->indexContent($this->title, $filteredGrid);
    }

    protected function filteredGrid($id)
    {
        $grid = $this->grid();
        $grid->model()->where('student_report_id', '=', $id);
        return $grid;
    }

}
