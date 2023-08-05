<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduStudentReport;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;


class Edu_StudentReportController extends AdminController
{

    /***
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Báo cáo học sinh';

    /***
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $reportDetailURL = function ($value) {
            if (!$value) return;
            // https://business.metaverse-solution.vn/admin/edu/report-student/$value
            // http://127.0.0.1:8000/admin/edu/report-student/$value
            return "<a href='https://business.metaverse-solution.vn/admin/edu/report-student/$value' style='text-decoration: underline'>Báo cáo chi tiết</a>";
        };

        $grid = new Grid(new EduStudentReport());
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Lịch học'));
        $grid->column('type', __('Loại báo cáo'))->display(function ($type) {
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "ReportType", "description_vi", $type);
        });
        $grid->column('report_date', __('Ngày báo cáo'))->display(function ($reportDate) {
            return ConstantHelper::dateFormatter($reportDate);
        });
        $grid->column('lesson_name', __('Tên bài giảng'));
        $grid->column('id', __('Báo cáo chi tiết'))->display($reportDetailURL);
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->model()->where('business_id', '=', Admin::user()->business_id);
        $grid->fixColumns(0, 0);

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(EduStudentReport::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('schedule.name', __('Lịch học'));
        $show->field('type', __('Loại báo cáo'))->as(function ($type) {
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "ReportType", "description_vi", $type);
        });
        $show->field('report_date', __('Ngày báo cáo'));
        $show->field('lesson_name', __('Tên bài giảng'));
        $show->field('home_work', __('Bài tập'));
        $show->field('general_comment', __('Bài luận chung'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusDetailFormatter($status);
        });
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));

        return $show;
    }

    /**
     * Make a form builder
     *
     * @return Form
     */
    protected function form()
    {
        $branchs = (new UtilsCommonHelper)->optionsBranch();
        $business = (new UtilsCommonHelper)->currentBusiness();

        $reportTypeOptions = (new UtilsCommonHelper)->commonCode("Edu", "ReportType", "description_vi", "value");
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();

        $form = new Form(new EduStudentReport());
        $form->hidden('business_id')->value($business->id);
        if ($form->isEditing()) {
            $id = request()->route()->parameter('student_report');
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $schedules = (new UtilsCommonHelper)->optionsScheduleByBranchId($branchId);
            $scheduleId = $form->model()->find($id)->getOriginal("schedule_id");

            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options($schedules)->default($scheduleId)->required();
        } else {
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options()->required();
        }
        $form->select('type', __('Loại báo cáo'))->options($reportTypeOptions)->required();
        $form->date('report_date', __('Ngày báo cáo'));
        $form->text('lesson_name', __('Tên bài giảng'));
        $form->textarea('home_work', __('Bài tập'));
        $form->textarea('general_comment', __('Bình luận chung'));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault);


        $urlSchedule = 'https://business.metaverse-solution.vn/api/schedule';

        $script = <<<EOT
        $(function() {
            var branchSelect = $(".branch_id");
            var scheduleSelect = $(".schedule_id");
            var optionsSchedule = {};

            branchSelect.on('change', function() {

                scheduleSelect.empty();
                optionsSchedule = {};
                $("#class_name").val("")

                var selectedBranchId = $(this).val();
                if(!selectedBranchId) return
                $.get("$urlSchedule", { branch_id: selectedBranchId }, function (schedules) {
                    var schedulesActive = schedules.filter(function (cls) {
                        return cls.status === 1;
                    });                    
                    $.each(schedulesActive, function (index, cls) {
                        optionsSchedule[cls.id] = cls.name;
                    });
                    scheduleSelect.empty();
                    scheduleSelect.append($('<option>', {
                        value: '',
                        text: ''
                    }));
                    $.each(optionsSchedule, function (id, scheduleName) {
                        scheduleSelect.append($('<option>', {
                            value: id,
                            text: scheduleName
                        }));
                    });
                    scheduleSelect.trigger('change');
                });
            });
        });
        
        EOT;
        Admin::script($script);
        return $form;
    }
}
