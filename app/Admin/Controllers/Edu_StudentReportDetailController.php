<?php

namespace App\Admin\Controllers;

use App\Admin\Grid\CustomEditAction;
use App\Http\Models\Core\Branch;
use App\Http\Models\Edu\EduSchedule;
use App\Http\Models\Edu\EduStudent;
use App\Http\Models\Edu\EduStudentReport;
use App\Http\Models\Edu\EduStudentReportDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\View;
use Encore\Admin\Form;
use Encore\Admin\Widgets\Table;

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
        $harkwork = function ($value) {
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Harkwork", "description_vi", $value);
        };

        $lastHomework = function ($value) {
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "LastHomework", "description_vi", $value);
        };

        $grid = new Grid(new EduStudentReportDetail());

        $grid->column('student.name', __('Tên học sinh'))->modal('Thông tin học sinh', function ($model) {

            $comments = EduStudent::where("id", $model->student_id)->take(10)->get()->map(function ($student) {
                $grade = UtilsCommonHelper::commonCodeGridFormatter("Edu", "Grade", "description_vi", $student->grade);
                $channel = UtilsCommonHelper::commonCodeGridFormatter("Edu", "Channel", "description_vi", $student->channel);
                $wom = UtilsCommonHelper::commonCodeGridFormatter("Edu", "WOM", "description_vi", $student->wom);
                $location = UtilsCommonHelper::commonCodeGridFormatter("Edu", "Location", "description_vi", $student->location);
                $school = UtilsCommonHelper::commonCodeGridFormatter("Edu", "School", "description_vi", $student->school);

                $item = $student->only(['name', 'channel', 'wom', 'student_phone_number', 'parent', 'phone_number',]);
                $item['channel'] = $channel;
                $item['wom'] = $wom;
                $item['grade'] = $grade;
                $item['location'] = $location;
                $item['school'] = $school;
                return $item;
            });
            return new Table(['Tên học sinh', 'Kênh', 'WOM', 'SĐT học sinh', 'Bố mẹ', 'SĐT bố hoặc mẹ', 'Khối', "Địa chỉ", "Trường"], $comments->toArray());
        });
        $grid->column('harkwork', __('Chuyên cần'))->display($harkwork);
        $grid->column('last_homework', __('Bài tập cuối'))->display($lastHomework);
        $grid->column('mini_test', __('Kiểm tra ngắn'));
        $grid->column('home_work', __('Bài tập về nhà'));
        $grid->column('comment', __('Bình luận'))->display(function($comment){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "CommentStudent", "description_vi", $comment);
        });
        $grid->column('parent_comment', __('Bố mẹ bình luận'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusGridFormatter($status);
        });
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
            $show = new Show(EduStudentReportDetail::findOrFail($id));
            $harkwork = function ($value) {
                return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Harkwork", "description_vi", $value);
            };

            $lastHomework = function ($value) {
                return UtilsCommonHelper::commonCodeGridFormatter("Edu", "LastHomework", "description_vi", $value);
            };
            $show->field('student.name', __('Tên học sinh'));
            $show->field('harkwork', __('Chuyên cần'))->as($harkwork);
            $show->field('last_homework', __('Bài tập cuối'))->as($lastHomework);
            $show->field('mini_test', __('Kiểm tra ngắn'));
            $show->field('home_work', __('Bài tập về nhà'));
            $show->field('comment', __('Bình luận'))->as(function($comment){
                return UtilsCommonHelper::commonCodeGridFormatter("Edu", "CommentStudent", "description_vi", $comment);
            });
            $show->field('parent_comment', __('Bố mẹ bình luận'));
            $show->field('status', __('Trạng thái'))->as(function ($status) {
                return UtilsCommonHelper::statusDetailFormatter($status);
            });
            $show->field('created_at', __('Ngày tạo'));
            $show->field('updated_at', __('Ngày cập nhật'));
            $show->panel()
            ->tools(function ($tools) {
                $tools->disableList();
                $tools->disableDelete();
            });;
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
        $commentStudentOptions = (new UtilsCommonHelper)->commonCode("Edu", "CommentStudent", "description_vi", "value");


        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();

        $form = new Form(new EduStudentReportDetail());
        $form->select('harkwork', __('Chuyên cần'))->options($harkWorkOptions);
        $form->select('last_homework', __('Bài tập cuối'))->options($lastHomeworkOptions);
        $form->textarea('mini_test', __('Kiểm tra ngắn'));
        $form->textarea('home_work', __('Bài tập về nhà'));
        $form->select('comment', __('Bình luận học sinh'))->options($commentStudentOptions);
        $form->textarea('parent_comment', __('Bố mẹ bình luận'));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableList();
        });
        $form->saved(function (Form $form) {
            admin_toastr('Lưu thành công!');
            $id = request()->route()->parameter('report_detail');
            $studentReportId = $form->model()->find($id)->getOriginal("student_report_id");
            return redirect("/admin/edu/report-student/{$studentReportId}");
        });
        return $form;
    }

}
