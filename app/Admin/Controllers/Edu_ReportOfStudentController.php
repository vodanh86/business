<?php

namespace App\Admin\Controllers;

class Edu_ReportOfStudentController extends Edu_StudentReportDetailController
{
    protected $title = 'Báo cáo';

    public function index($id)
    {
        $filteredGrid = $this->grid($id);
        return $this->indexContent($this->title, $filteredGrid);
    }

}


