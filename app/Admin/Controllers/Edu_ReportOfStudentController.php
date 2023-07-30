<?php

namespace App\Admin\Controllers;

class Edu_ReportOfStudentController extends Edu_StudentReportController
{
    protected $title = 'Báo cáo chi tiết học dinh';

    protected function grid()
    {
        return $this->search(1);
    }
}