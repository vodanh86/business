<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduStudent;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;

class Edu_StudentController extends AdminController{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Học sinh';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduStudent());

        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Tên lịch học'));
        $grid->column('channel', __('Kênh'))->display(function($channel){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Channel", "description_vi", $channel);
        });
        $grid->column('wom', __('WOM'))->display(function($wom){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "WOM", "description_vi", $wom);
        });
        $grid->column('source', __('Nguồn'));
        $grid->column('name', __('Tên học sinh'));
        $grid->column('parent', __('Bố mẹ'));
        $grid->column('phone_number', __('Số điện thoại'));
        $grid->column('last_call', __('Liên lạc gần nhất'));
        $grid->column('contact_status', __('Trạng thái liên lạc'));
        $grid->column('grade', __('Khối'))->display(function($grade){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Grade", "description_vi", $grade);
        });
        $grid->column('location', __('Địa chỉ'))->display(function($location){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Location", "description_vi", $location);
        });
        $grid->column('school', __('Trường'))->display(function($grade){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Grade", "description_vi", $grade);
        });
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusGridFormatter($status);
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->fixColumns(0, 0);
        $grid->disableExport();
       
        
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
        $show = new Show(EduStudent::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'))->width(150);
        $show->field('schedule.name', __('Tên lịch học'))->width(150);
        $show->field('channel', __('Kênh'))->as(function($channel){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Channel", "description_vi", $channel);
        });
        $show->field('wom', __('WOM'))->as(function($wom){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "WOM", "description_vi", $wom);
        });
        $show->field('source', __('Nguồn'));
        $show->field('name', __('Tên học sinh'));
        $show->field('parent', __('Bố mẹ'));
        $show->field('phone_number', __('Số điện thoại'));
        $show->field('last_call', __('Liên lạc gần nhất'));
        $show->field('contact_status', __('Trạng thái liên lạc'));
        $show->field('grade', __('Khối'))->as(function($grade){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Grade", "description_vi", $grade);
        });
        $show->field('location', __('Địa chỉ'))->as(function($location){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Location", "description_vi", $location);
        });
        $show->field('school', __('Trường'))->as(function($grade){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Grade", "description_vi", $grade);
        });
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $school = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "School")->pluck('description_vi','value');
        $channel = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Channel")->pluck('description_vi','value');
        $wom = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "WOM")->pluck('description_vi','value');
        $grade = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Grade")->pluck('description_vi','value');
        $location = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Location")->pluck('description_vi','value');
        
        $branchs = (new UtilsCommonHelper)->optionsBranch();
        $business = (new UtilsCommonHelper)->currentBusiness();

        $form = new Form(new EduStudent());
        $form->hidden('business_id')->value($business->id);
        if ($form->isEditing()) {
            $id = request()->route()->parameter('student');
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $schedules = (new UtilsCommonHelper)->optionsScheduleByBranchId($branchId);
            $scheduleId = $form->model()->find($id)->getOriginal("schedule_id");

            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId);
            $form->select('schedule_id', __('Tên lịch học'))->options($schedules)->default($scheduleId);
            // $form->text('class_name', __('Tên lớp học'))->value()->disable()->required();
        } else {
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options()->required();
            $form->text('class_name', __('Tên lớp học'))->disable()->required();
        }
        $form->select('channel', __('Kênh'))->options($channel);
        $form->select('wom', __('WOM'))->options($wom);
        $form->text('source', __('Nguồn'));
        $form->text('name', __('Tên học sinh'));
        $form->text('parent', __('Bố mẹ'));
        $form->mobile('phone_number', __('Số điện thoại'))->options(['mask' => '999 999 9999']);
        $form->text('last_call', __('Liên lạc gần nhất'));
        $form->text('contact_status', __('Trạng thái liên lạc'));
        $form->select('grade', __('Khối'))->options($grade);
        $form->select('location', __('Địa chỉ'))->options($location);
        $form->select('school', __('Trường'))->options($school);
        $form->select('status', __('Trạng thái'))->options($status);
      

        $urlSchedule = 'https://business.metaverse-solution.vn/api/schedule';
        $urlScheduleById = 'https://business.metaverse-solution.vn/api/schedule/get-by-id';
        $urlClassById = 'https://business.metaverse-solution.vn/api/class/get-by-id';
        $urlStudent = 'https://business.metaverse-solution.vn/api/student';
        
        $script = <<<EOT
        $(function() {
            var branchSelect = $(".branch_id");
            var scheduleSelect = $(".schedule_id");
            var studentSelect = $(".student_id");
            var optionsSchedule = {};
            var optionsStudent = {};

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

            scheduleSelect.on('change', function() {

                studentSelect.empty();
                $("#class_name").val("")

                var selectedScheduleId = $(this).val();
                if(!selectedScheduleId) return
                $.get("$urlScheduleById", { q: selectedScheduleId }, function (schedule) {

                    $.get("$urlClassById", { q: schedule.class_id }, function (cls) {
                        $("#class_name").val(cls.name)
                    });

                    $.get("$urlStudent", { schedule_id: schedule.class_id }, function (students) {
                        var studentsActive = students.filter(function (student) {
                            return student.status === 1 && student.class_id === schedule.class_id
                        });      
                        $.each(studentsActive, function (index, student) {
                            optionsStudent[student.id] = student.name;
                        });
                        studentSelect.empty();
                        studentSelect.append($('<option>', {
                            value: '',
                            text: ''
                        }));
                        $.each(optionsStudent, function (id, studentName) {
                            studentSelect.append($('<option>', {
                                value: id,
                                text: studentName
                            }));
                        });
                        studentSelect.trigger('change');
                    });
                });
            });
        });
        
        EOT;
        Admin::script($script);
        return $form;
    }
}