<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduClass;
use App\Http\Models\Edu\EduSchedule;
use App\Http\Models\Edu\EduTeacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_ScheduleController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Lịch học';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
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
        $day = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "daysofweek")->pluck('description_vi','value');
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new EduSchedule());
        
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('class.name', __('Tên lớp'));
        $grid->column('name', __('Tên lịch học'));
        $grid->column('teacher.name', __('Tên giảng viên'));
        $grid->column('day', __('Ngày'))->display(function ($value) use ($day) {
            if (!is_array($value)) {
                return '';
            }
            $dayDescriptions = array_map(function ($dayValue) use ($day) {
                return $day[$dayValue] ?? '';
            }, $value);
            $dayDescriptions = array_filter($dayDescriptions);
            if (empty($dayDescriptions)) {
                return '';
            }
            $dayValueType = join(", ", $dayDescriptions);
            return "<span class='label label-success'>$dayValueType</span>";
        });
        
        $grid->column('start_time', __('Thời gian bắt đầu'));
        $grid->column('end_time', __('Thời gian kết thúc'));
        $grid->column('duration', __('Khoảng thời gian(giờ)'));
        $grid->column('status', __('Trạng thái'))->display($status);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $show = new Show(EduSchedule::findOrFail($id));
        $day = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "daysofweek")->pluck('description_vi','value');

        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('class.name', __('Tên lớp'));
        $show->field('name', __('Tên lịch học'));
        $show->field('teacher.name', __('Tên giảng viên'));
        $show->field('day', __('Ngày'))->as(function ($value) use ($day) {
            return $day[$value] ?? '';
        });
        $show->field('start_time', __('Thời gian bắt đầu'));
        $show->field('end_time', __('Thời gian kết thúc'));
        $show->field('duration', __('Khoảng thời gian(giờ)'));
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
        $allClass= EduClass::all()->pluck('name', 'id');
        $branchesBiz = Branch::where('business_id', Admin::user()->business_id)->pluck('branch_name', 'id');
        $teacher = EduTeacher::where('business_id', Admin::user()->business_id)->where('status', 1)->pluck("name", "id");
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $day = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "daysofweek")->pluck('description_vi','value');
        $form = new Form(new EduSchedule());
        $form->divider('1. Thông tin cơ bản');
        $form->hidden('business_id')->value($business->id);
        if ($form->isEditing()) {
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->default(function ($id) use ($allBranches) {
                return $id ? [$id => $allBranches[$id]] : $allBranches;
            });
            $form->select('class_id', __('Tên lớp học'))->options()->default(function ($id) use ($allClass) {
                return $id ? [$id => $allClass[$id]] : $allClass;
            });
        }else{
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->required();
            $form->select('class_id', __('Tên lớp học'))->options()->required();
        }
        $form->select('teacher_id', __('Tên giảng viên'))->options($teacher)->required();

        
        $form->divider('2. Thông tin lịch học');
        $form->text('name', __('Tên lịch học'))->required();
        $form->multipleSelect('day', __('Ngày'))->options($day)->required();
        $form->text('start_time', __('Thời gian bắt đầu'))->required();
        $form->text('end_time', __('Thời gian kết thúc'))->required();
        $form->text('duration', __('Khoảng thời gian(giờ)'))->required();
        $form->select('status', __('Trạng thái'))->options($status)->required();

        // $urlBranch = env('APP_URL') . '/api/branch';
        // $urlBusiness = env('APP_URL') . '/api/business';
        $urlClass = 'https://business.metaverse-solution.vn/api/class';

        $script = <<<EOT
        $(function() {
            var branchSelect = $(".branch_id");
            var classSelect = $(".class_id");
            var optionsClass = {};

            branchSelect.on('change', function() {
                classSelect.empty();
                optionsClass = {};
                var selectedBranchId = $(this).val();
                if(!selectedBranchId) return
                $.get("$urlClass", { branch_id: selectedBranchId }, function (classes) {
                    var classesActive = classes.filter(function (cls) {
                        return cls.status === 1;
                    });                    
                    $.each(classesActive, function (index, cls) {
                        optionsClass[cls.id] = cls.name;
                    });
                    classSelect.empty();
                    classSelect.append($('<option>', {
                        value: '',
                        text: ''
                    }));
                    $.each(optionsClass, function (id, className) {
                        classSelect.append($('<option>', {
                            value: id,
                            text: className
                        }));
                    });
                    classSelect.trigger('change');
                });
            });
        });
        
        EOT;
        Admin::script($script);
        return $form;
    }
}