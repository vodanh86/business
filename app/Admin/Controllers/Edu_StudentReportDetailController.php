<?php

namespace App\Admin\Controllers;

use App\Admin\Grid\CustomEditAction;
use App\Admin\Grid\CustomViewAction;
use App\Http\Models\Core\Branch;
use App\Http\Models\Edu\EduSchedule;
use App\Http\Models\Edu\EduStudentReport;
use App\Http\Models\Edu\EduStudentReportDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
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

    /**
     * Make a grid builder.
     *
     * @param int||array $student_report_ids
     * @return Grid
     */
    protected function grid($student_report_ids = [])
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
        $grid->fixColumns(0, 0);
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            // $actions->append(new CustomViewAction($actions->getKey()));
            $actions->append(new CustomEditAction($actions->getKey()));
        });
        if (!empty($student_report_ids)) {
            $grid->model()->whereIn('id', $student_report_ids);
        }
        return $grid;
    }

    /**
     * Show the detail of a specific student report.
     *
     * @param int $id
     * @return Show
     */
    public function detail($id)
    {
        if (request()->is('admin/edu/report-student/*')) {
            $report = EduStudentReport::findOrFail($id);
            $branch = Branch::all()->where("id", $report->branch_id)->first();
            $schedule = EduSchedule::all()->where("id", $report->schedule_id)->first();
            $typeReport = UtilsCommonHelper::commonCodeGridFormatter("Edu", "ReportType", "description_vi", $report->type);


            $reportDetails = EduStudentReportDetail::where("student_report_id", $id)->get();
            $reportDetailIds = $reportDetails->pluck('id')->toArray();
            $filteredGrid = $this->grid($reportDetailIds);


            return View::make('admin.student_report_detail', compact('report', 'branch', 'schedule', 'typeReport', 'filteredGrid'));
        } else {
            $harkwork = function ($value) {
                return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Harkwork", "description_vi", $value);
            };

            $lastHomework = function ($value) {
                return UtilsCommonHelper::commonCodeGridFormatter("Edu", "LastHomework", "description_vi", $value);
            };
            $show = new Show(EduStudentReport::findOrFail($id));

            $show->field('student.name', __('Tên học sinh'));
            $show->field('harkwork', __('Chuyên cần'))->as($harkwork);
            $show->field('last_homework', __('Bài tập cuối'))->as($lastHomework);
            $show->field('mini_test', __('Kiểm tra ngắn'));
            $show->field('home_work', __('Bài tập về nhà'));
            $show->field('comment', __('Bình luận'));
            $show->field('parent_comment', __('Bố mẹ bình luận'));
            $show->field('status', __('Trạng thái'))->as(function ($status) {
                return UtilsCommonHelper::statusDetailFormatter($status);
            });
            $show->field('created_at', __('Ngày tạo'));
            $show->field('updated_at', __('Ngày cập nhật'));

            return $show;
        }
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $harkWorkOptions = (new UtilsCommonHelper)->commonCode("Edu", "Harkwork", "description_vi", "value");
        $lastHomeworkOptions = (new UtilsCommonHelper)->commonCode("Edu", "LastHomework", "description_vi", "value");
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();
        $form = new Form(new EduStudentReportDetail());
        $form->select('harkwork', __('Chuyên cần'))->options($harkWorkOptions);
        $form->select('last_homework', __('Bài tập cuối'))->options($lastHomeworkOptions);
        $form->textarea('mini_test', __('Kiểm tra ngắn'));
        $form->textarea('home_work', __('Bài tập về nhà'));
        $form->textarea('comment', __('Bình luận học sinh'));
        $form->textarea('parent_comment', __('Bố mẹ bình luận'));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableList();
        });
        return $form;
    }

}
