<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduClass;
use App\Http\Models\Edu\EduSchedule;
use App\Http\Models\Edu\EduStudent;
use App\Http\Models\Edu\EduStudentReportDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_StudentReportDetailController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Báo cáo chi tiết học sinh';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $status = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Status')
            ->where('value', $value)
            ->first();
            if ($commonCode) {
                return $value === 1 ? "<span class='label label-success'>$commonCode->description_vi</span>" : "<span class='label label-danger'>$commonCode->description_vi</span>";
            }
            return '';
        };
        $harkwork = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Harkwork')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };
        $lastHomework = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'LastHomework')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };

        $grid = new Grid(new EduStudentReportDetail());
        $grid->column('student.name', __('Tên học sinh'));
        $grid->column('harkwork', __('Chuyên cần'))->display($harkwork);
        $grid->column('last_homework', __('Bài tập về nhà'))->display($lastHomework);
        $grid->column('status', __('Trạng thái'))->display($status);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        // $id = request()->route();
        // dd($id);
        // $model = $grid->model()->find($id);
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
        $status = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Status')
            ->where('value', $value)
            ->first();
            if ($commonCode) {
                return $value === 1 ? "<span class='label label-success'>$commonCode->description_vi</span>" : "<span class='label label-danger'>$commonCode->description_vi</span>";
            }
            return '';
        };
        $harkwork = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Harkwork')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };
        $lastHomework = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'LastHomework')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };

        $show = new Show(EduStudentReportDetail::findOrFail($id));

       
        $show->field('student.name', __('Tên học sinh'));
        $show->field('harkwork', __('Chuyên cần'))->as($harkwork);
        $show->field('last_homework', __('Bài tập về nhà'))->as($lastHomework);
        $show->field('studentReport.report_date', __('Ngày báo cáo'));
        $show->field('studentReport.general_comment', __('Bình luận chung'));
        $show->field('status', __('Trạng thái'))->as($status);
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
       
        return $show;
    }
     /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $business = Business::where('id', Admin::user()->business_id)->first();
        $branchesBiz = Branch::where('business_id', Admin::user()->business_id)->pluck('branch_name', 'id');
        $allSchedule = EduSchedule::all()->pluck('name', 'id');

        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $harkwork = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Harkwork")->pluck('description_vi','value');
        $lastHomework = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "LastHomework")->pluck('description_vi','value');

        $form = new Form(new EduStudentReportDetail());
        if ($form->isEditing()) {
            $id = request()->route()->parameter("report_detail");
            $model = $form->model()->find($id);
            $branchName = $model->branch->branch_name;

            $form->hidden('business_id')->value($business->id);
            $form->text('branch_id', __('Tên chi nhánh'))->value($branchName)->disable();
            $form->select('schedule_id', __('Tên lịch học'))->options($allSchedule)->default(function ($id) {
                $schedule = EduSchedule::find($id);
                if ($schedule) {
                    return [$schedule->id => $schedule->name];
                }
            })->required();
            $form->select('harkwork', __('Chuyên cần'))->options($harkwork)->required();
            $form->select('last_homework', __('Bài tập về nhà'))->options($lastHomework)->required();
            $form->select('status', __('Trạng thái'))->options($status)->required();
        }
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        
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