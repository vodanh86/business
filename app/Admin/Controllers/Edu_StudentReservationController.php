<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduStudent;
use App\Http\Models\Edu\EduStudentReservation;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Table;


class Edu_StudentReservationController extends AdminController{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Học sinh bảo lưu';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduStudentReservation());

        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Tên lịch học'))->width(150);
        $grid->column('student.name', __('Tên học sinh'))->modal('Thông tin học sinh', function ($model) {

            $comments = EduStudent::where("id", $model->student_id)->take(10)->get()->map(function ($student) {
                $grade = UtilsCommonHelper::commonCodeGridFormatter("Edu", "Grade", "description_vi", $student->grade);
                $channel = UtilsCommonHelper::commonCodeGridFormatter("Edu", "Channel", "description_vi", $student->channel);
                $wom = UtilsCommonHelper::commonCodeGridFormatter("Edu", "WOM", "description_vi", $student->wom);
                $location = UtilsCommonHelper::commonCodeGridFormatter("Edu", "Location", "description_vi", $student->location);
                $school = UtilsCommonHelper::commonCodeGridFormatter("Edu", "School", "description_vi", $student->school);

                $item = $student->only(['channel', 'wom', 'source', 'student_phone_number', 'student_email', 'parent', 'phone_number', 'last_call', 'contact_status']);
                $item['channel'] = $channel;
                $item['wom'] = $wom;
                $item['grade'] = $grade;
                $item['location'] = $location;
                $item['school'] = $school;
                return $item;
            });
            return new Table(['Kênh', 'WOM', 'Nguồn', 'SĐT h/s', 'Email h/s', 'Bố mẹ', 'SĐT bố hoặc mẹ', 'Liên lạc gần nhất', 'Trạng thái', 'Khối', "Địa chỉ", "Trường"], $comments->toArray());
        });
        $grid->column('reservation_date', __('Ngày bảo lưu'))->display(function ($reservationDate) {
            return ConstantHelper::dayFormatter($reservationDate);
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
        $show = new Show(EduStudentReservation::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'))->width(150);
        $show->field('schedule.name', __('Tên lịch học'))->width(150);
        $show->field('student.name', __('Tên học sinh'))->width(150);
        $show->field('student.channel', __('Kênh'))->as(function($channel){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Channel", "description_vi", $channel);
        });
        $show->field('student.wom', __('WOM'))->as(function($wom){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "WOM", "description_vi", $wom);
        });
        $show->field('student.source', __('Nguồn'));
        $show->field('student.name', __('Tên học sinh'));
        $show->field('student.student_phone_number', __('SĐT học sinh'));
        $show->field('student.student_email', __('Email học sinh'));
        $show->field('student.parent', __('Bố mẹ'));
        $show->field('student.phone_number', __('SĐT bố hoặc mẹ'));
        $show->field('student.parent_email', __('Email bố hoặc mẹ'));
        $show->field('student.last_call', __('Liên lạc gần nhất'));
        $show->field('student.contact_status', __('Trạng thái liên lạc'));
        $show->field('student.grade', __('Khối'))->as(function($grade){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Grade", "description_vi", $grade);
        });
        $show->field('student.location', __('Địa chỉ'))->as(function($location){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "Location", "description_vi", $location);
        });
        $show->field('student.school', __('Trường'))->as(function($grade){
            return UtilsCommonHelper::commonCodeGridFormatter("Edu", "School", "description_vi", $grade);
        });
        $show->field('reservation_date', __('Ngày bảo lưu'))->display(function ($reservationDate) {
            return ConstantHelper::dayFormatter($reservationDate);
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
        $daysOfWeek = (new UtilsCommonHelper)->commonCode("Edu", "daysofweek", "description_vi", "value");
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();
        $business = (new UtilsCommonHelper)->currentBusiness();
        $branchs = (new UtilsCommonHelper)->optionsBranch();

        $form = new Form(new EduStudentReservation());
        $form->hidden('business_id')->value($business->id);
        if ($form->isEditing()) {
            $id = request()->route()->parameter('student_reservation');
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $schedules = (new UtilsCommonHelper)->optionsScheduleByBranchId($branchId);
            $scheduleId = $form->model()->find($id)->getOriginal("schedule_id");
            $students = (new UtilsCommonHelper)->optionsStudentByScheduleId($scheduleId);
            $studentId = $form->model()->find($id)->getOriginal("student_id");

            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId);
            $form->select('schedule_id', __('Tên lịch học'))->options($schedules)->default($scheduleId)->required()->readonly();
            $form->select('student_id', __('Tên học sinh'))->options($students)->default($studentId)->required()->readonly();
        } else {
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options()->required()->disable();
            $form->text('class_name', __('Tên lớp học'))->disable()->required();
            $form->select('student_id', __('Tên học sinh'))->options()->required()->disable();
        }
        $form->date('reservation_date', __('Ngày bảo lưu'))->required();
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();
        $urlSchedule = 'https://business.metaverse-solution.vn/api/schedule';
        $urlScheduleById = 'https://business.metaverse-solution.vn/api/schedule/get-by-id';
        $urlClassById = 'https://business.metaverse-solution.vn/api/class/get-by-id';
        $urlStudent = 'https://business.metaverse-solution.vn/api/student';

        $script = <<<EOT
        $(function() {
            var branchSelect = $(".branch_id");
            var scheduleSelect = $(".schedule_id");
            var scheduleSelectDOM = document.querySelector('.schedule_id');
            var studentSelect = $(".student_id");
            var studentSelectDOM = document.querySelector('.student_id');
            var optionsSchedule = {};
            var optionsStudent = {};

            branchSelect.on('change', function() {

                scheduleSelect.empty();
                optionsSchedule = {};
                $("#class_name").val("")

                var selectedBranchId = $(this).val();
                if(!selectedBranchId) return
                $.get("$urlSchedule", { branch_id: selectedBranchId }, function (schedules) {
                    scheduleSelectDOM.removeAttribute('disabled');
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
                studentSelectDOM.setAttribute("disabled", false);
                optionsStudent = {};
                $("#class_name").val("")

                var selectedScheduleId = $(this).val();
                if(!selectedScheduleId) return

                $.get("$urlScheduleById", { q: selectedScheduleId }, function (schedule) {

                    $.get("$urlClassById", { q: schedule.class_id }, function (cls) {
                        $("#class_name").val(cls.name)
                    });

                    $.get("$urlStudent", { schedule_id: schedule.id }, function (students) {
                        studentSelectDOM.removeAttribute('disabled');
                        var studentsActive = students.filter(function (student) {
                            return student.status === 1 && student.schedule_id === schedule.id
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
