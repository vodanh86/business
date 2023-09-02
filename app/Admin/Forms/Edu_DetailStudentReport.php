<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Edu_DetailStudentReport extends Form
{
    /**
     * The form title.
     *
     * @var  string
     */
    public $title = 'Thông tin';

    /**
     * Handle the form request.
     *
     * @param  Request $request
     *
     * @return  \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $result = array("student_id" => $request->get("student_id"),
                        "type" => $request->get("type")
                        );
        return back()->with(['result' => $result]);
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('student_id', 'ID học sinh')->width(2);
        $this->radio('type', 'Loại báo cáo')->options(['detail' => 'Báo cáo chi tiết','tuition_collection' => 'Báo cáo học phí'])->default('detail');
    }

    /**
     * The data of the form.
     *
     * @return  array $data
     */
    public function data()
    {
        if ($data = session('result')) {
            return $data;
        }
        return [
            'student_id' => ''
        ];
    }
}