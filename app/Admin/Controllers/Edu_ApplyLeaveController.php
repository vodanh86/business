<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduApplyLeave;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;

class Edu_ApplyLeaveController extends AdminController{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Đăng ký nghỉ';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduApplyLeave());
        
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Tên lịch học'));
        $grid->column('leave_date', __('Ngày đăng ký nghỉ'));
        $grid->column('reason', __('Lý do'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusGridFormatter($status);
        });
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
        $show = new Show(EduApplyLeave::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('schedule.name', __('Tên lịch học'));
        $show->field('leave_date', __('Ngày đăng ký nghỉ'));
        $show->field('reason', __('Lý do'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusDetailFormatter($status);
        });
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
        $business = (new UtilsCommonHelper)->currentBusiness();
        $branchs = (new UtilsCommonHelper)->optionsBranch();
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();
       
        $form = new Form(new EduApplyLeave());
        $form->hidden('business_id')->value($business->id);

        if ($form->isEditing()) {
            $id = request()->route()->parameter('apply-leave');
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $schedules = (new UtilsCommonHelper)->optionsScheduleByBranchId($branchId);
            $scheduleId = $form->model()->find($id)->getOriginal("schedule_id");

            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId);
            $form->select('schedule_id', __('Tên lịch học'))->options($schedules)->default($scheduleId);
        }else{
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options()->required();
        }

        
        $form->text('leave_date', __('Ngày đăng ký nghỉ'));
        $form->text('reason', __('Lý do'));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();

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