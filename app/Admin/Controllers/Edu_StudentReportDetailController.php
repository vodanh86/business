<?php

namespace App\Admin\Controllers;

use App\Admin\Grid\CustomEditAction;
use App\Http\Models\Core\Branch;
use App\Http\Models\Edu\EduSchedule;
use App\Http\Models\Edu\EduStudentReport;
use App\Http\Models\Edu\EduStudentReportDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\View;
use Encore\Admin\Form;


class Edu_StudentReportDetailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Báo cáo';

    public function index(Content $content)
    {
        $grid = $this->grid();

        return $content
            ->header($this->title)
            ->body($grid);
    }

    /**
     * Make a grid builder.
     *
     * @param int|null $student_report_id
     * @return Grid
     */
    protected function grid($student_report_id = null)
    {
        $status = function ($value) {
            return UtilsCommonHelper::commonCodeGridFormatter("Core", "Status", "description_vi", $value);
        };

        $harkwork = function ($value) {
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Harkwork", "description_vi", $value);
        };

        $lastHomework = function ($value) {
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "LastHomework", "description_vi", $value);
        };

        $grid = new Grid(new EduStudentReportDetail());

        $grid->column('student.name', __('Tên học sinh'));
        $grid->column('harkwork', __('Chuyên cần'))->display($harkwork);
        $grid->column('last_homework', __('Bài tập cuối'))->display($lastHomework);
        $grid->column('mini_test', __('Kiểm tra ngắn'));
        $grid->column('home_work', __('Bài tập về nhà'));
        $grid->column('comment', __('Bình luận'));
        $grid->column('parent_comment', __('Bố mẹ bình luận'));
        $grid->column('status', __('Trạng thái'))->display($status);
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return date('d/m/Y - H:i:s', strtotime($createdAt));
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return date('d/m/Y - H:i:s', strtotime($updatedAt));
        });
        $grid->fixColumns(0,0);
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions->append(new CustomEditAction($actions->getKey()));
        });
        if ($student_report_id !== null) {
            $grid->model()->where('id', $student_report_id);
        }

        return $grid;
    }

    /**
     * Show the detail of a specific student report.
     *
     * @param int $id
     * @return Content
     */
    public function detail($id)
    {
        $report = EduStudentReport::findOrFail($id);
        $branch = Branch::all()->where("id", $report->branch_id)->first();
        $schedule = EduSchedule::all()->where("id", $report->schedule_id)->first();
        $typeReport = UtilsCommonHelper::commonCodeGridFormatter("Edu", "ReportType", "description_vi", $report->type);

        $reportDetail = EduStudentReportDetail::all()->where("student_report_id", $id)->first();
        $reportDetailId = $reportDetail->id;
        $filteredGrid = $this->grid($reportDetailId);
        
        return View::make('admin.student_report_detail', compact('report', 'branch', 'schedule', 'typeReport', 'filteredGrid'));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EduStudentReportDetail());
        $form->select('hardword', __('Loại báo cáo'))->options()->required();
        $form->date('last_homeword', __('Ngày báo cáo'));
        $form->text('mini_test', __('Tên bài giảng'));
        $form->textarea('home_work', __('Bài tập'));
        $form->textarea('comment', __('Bình luận chung'));
        $form->textarea('parent_comment', __('Bình luận chung'));
        $form->select('status', __('Trạng thái'))->options()->required();

        return $form;
    }
}

