<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduApplyLeave;
use App\Http\Models\Edu\EduSchedule;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new EduApplyLeave());
        
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Tên lịch học'));
        $grid->column('leave_date', __('Ngày đăng ký nghỉ'));
        $grid->column('reason', __('Lý do'));
        $grid->column('status', __('Trạng thái'))->display(function ($value) use ($status) {
            return $status[$value] ?? '';
        });
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
        $grid->model()->where('business_id', '=', Admin::user()->business_id);
      
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $show = new Show(EduApplyLeave::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('schedule.name', __('Tên lịch học'));
        $show->field('leave_date', __('Ngày đăng ký nghỉ'));
        $show->field('reason', __('Lý do'));
        $show->field('status', __('Trạng thái'))->as(function ($value) use ($status) {
            return $status[$value] ?? '';
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
        $business = Business::where('id', Admin::user()->business_id)->first();
        $allBranches = Branch::where('business_id', Admin::user()->business_id)->where('status', 1)->pluck('branch_name', 'id');
        $allSchedule= EduSchedule::all()->pluck('name', 'id');
        $branchesBiz = Branch::where('business_id', Admin::user()->business_id)->pluck('branch_name', 'id');
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
       
        $form = new Form(new EduApplyLeave());
        $form->divider('1. Thông tin cơ bản');
        $form->hidden('business_id')->value($business->id);

        if ($form->isEditing()) {
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->default(function ($id) use ($allBranches) {
                return $id ? [$id => $allBranches[$id]] : $allBranches;
            });
            $form->select('schedule_id', __('Tên lịch học'))->options()->default(function ($id) use ($allSchedule) {
                return $id ? [$id => $allSchedule[$id]] : $allSchedule;
            });
        }else{
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options()->required();
        }

        
        $form->divider('2. Thông tin đăng ký nghỉ');
        $form->text('leave_date', __('Ngày đăng ký nghỉ'));
        $form->text('reason', __('Lý do'));
        $form->select('status', __('Trạng thái'))->options($status)->required();

        // $urlBranch = env('APP_URL') . '/api/branch';
        // $urlBusiness = env('APP_URL') . '/api/business';
        $urlClass = 'https://business.metaverse-solution.vn/api/class';

        $script = <<<EOT
        $(function() {
            var branchSelect = $(".branch_id");
            var scheduleSelect = $(".schedule_id");
            var optionsClass = {};

            branchSelect.on('change', function() {
                scheduleSelect.empty();
                optionsClass = {};
                var selectedBranchId = $(this).val();
                if(!selectedBranchId) return
                $.get("$urlClass", { branch_id: selectedBranchId }, function (schedules) {
                    var schedulesActive = schedules.filter(function (cls) {
                        return cls.status === 1;
                    });                    
                    $.each(schedulesActive, function (index, cls) {
                        optionsClass[cls.id] = cls.name;
                    });
                    scheduleSelect.empty();
                    scheduleSelect.append($('<option>', {
                        value: '',
                        text: ''
                    }));
                    $.each(optionsClass, function (id, className) {
                        scheduleSelect.append($('<option>', {
                            value: id,
                            text: className
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