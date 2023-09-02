<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduTuitionCollection;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;

class Edu_TuitionCollectionController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Thu học phí';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduTuitionCollection());
        $grid->column('trans_ref', __('Mã giao dịch'));
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Lịch học'));
        $grid->column('student.name', __('Học sinh'));
        $grid->column('processing_date', __('Ngày nộp tiền'))->display(function ($processingDate) {
            return ConstantHelper::dayFormatter($processingDate);
        });
        $grid->column('value_date', __('Ngày bắt đầu học'))->display(function ($valueDate) {
            return ConstantHelper::dayHightLightFormatter($valueDate, "valueDate");
        });
        $grid->column('next_date', __('Ngày tiếp theo'))->display(function ($nextDate) {
            return ConstantHelper::dayHightLightFormatter($nextDate, "nextDate");
        });
        $grid->column('amount', __('Số buổi đóng'));
        $grid->column('unit_price', __('Đơn giá'))->display(function ($unitPrice) {
            return ConstantHelper::moneyFormatter($unitPrice);
        });
        $grid->column('value', __('Giá trị'))->display(function ($value) {
            return ConstantHelper::moneyFormatter($value);
        });
        $grid->column('account_id', __('Số tài khoản'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountGridFormatter($accountNumber);
        });
        $grid->column('status', __('Trạng thái'))->display(function ($value) {
            return ConstantHelper::transactionGridRecordStatus($value);
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return ConstantHelper::dateFormatter($updatedAt);
        });

        $grid->model()
            ->select('id','trans_ref', 'business_id', 'branch_id', 'schedule_id', 'student_id', 'processing_date', 'value_date', 'next_date', 'amount', 'unit_price', 'value', 'account_id', 'status', 'created_at', 'updated_at')
           ->whereIn("status", [0,1]);

        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        $grid->model()->orderBy("next_date");
        $grid->fixColumns(0, 0);
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('trans_ref', 'Mã giao dịch');
        });
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
        $show = new Show(EduTuitionCollection::findOrFail($id));

        $show->field('trans_ref', __('Mã giao dịch'));
        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('schedule.name', __('Lịch học'));
        $show->field('student.name', __('Học sinh'));
        $show->field('processing_date', __('Ngày nộp tiền'))->as(function ($processingDate) {
            return ConstantHelper::dayFormatter($processingDate);
        });
        $show->field('value_date', __('Ngày bắt đầu học'))->as(function ($valueDate) {
            return ConstantHelper::dayFormatter($valueDate);
        });
        $show->field('next_date', __('Ngày tiếp theo'))->as(function ($nextDate) {
            return ConstantHelper::dayFormatter($nextDate);
        });
        $show->field('amount', __('Số buổi đóng'));
        $show->field('unit_price', __('Đơn giá'))->as(function ($unitPrice) {
            return ConstantHelper::moneyFormatter($unitPrice);
        });
        $show->field('value', __('Giá trị'))->as(function ($value) {
            return ConstantHelper::moneyFormatter($value);
        });
        $show->field('account_id', __('Tài khoản'))->as(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountDetailFormatter($accountNumber);
        });
        $show->field('description', __('Mô tả'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return ConstantHelper::transactionDetailRecordStatus($status);
        });
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });;
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
        $account = (new UtilsCommonHelper)->bankAccountFormFormatter();

        $form = new Form(new EduTuitionCollection());
        $tranferId = (new UtilsCommonHelper)->generateTransactionId("TC");
        $form->text("trans_ref", __('Mã giao dịch'))->default($tranferId)->readonly();
        $form->hidden('business_id')->value($business->id);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        if ($form->isEditing()) {
            $id = request()->route()->parameter('tuition_collection');
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $schedules = (new UtilsCommonHelper)->optionsScheduleByBranchId($branchId);
            $scheduleId = $form->model()->find($id)->getOriginal("schedule_id");
            $students = (new UtilsCommonHelper)->optionsStudentByScheduleId($scheduleId);
            $studentId = $form->model()->find($id)->getOriginal("student_id");
            dump($students);
            dump($studentId);
            $accountId = $form->model()->find($id)->getOriginal("account_id");

            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId)->required()->readonly();
            $form->select('schedule_id', __('Tên lịch học'))->options($schedules)->default($scheduleId)->required()->readonly();
            $form->select('student_id', __('Tên học sinh'))->options($students)->default($studentId)->required()->readonly();
            $form->date('processing_date', __('Ngày đóng tiền'))->required()->readonly();
            $form->date('value_date', __('Ngày bắt đầu học'))->required()->readonly();
            $form->currency('unit_price', __('Đơn giá'))->symbol('VND')->required()->readonly();
            $form->text('amount', __('Số buổi đóng'))->required()->readonly();
            $form->currency('value', __('Giá trị'))->symbol('VND')->disable()->readonly();
            $form->select('account_id', __('Số tài khoản'))->options($account)->default($accountId)->required()->readonly();
            $form->text('description', __('Mô tả'))->readonly();
        } else {
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options()->required()->disable();
            $form->text('class_name', __('Tên lớp học'))->disable()->required();
            $form->select('student_id', __('Tên học sinh'))->options()->required()->disable();
            $form->date('processing_date', __('Ngày đóng tiền'))->required();
            $form->date('value_date', __('Ngày bắt đầu học'))->required();
            $form->currency('unit_price', __('Đơn giá'))->symbol('VND')->required();
            $form->text('amount', __('Số buổi đóng'))->required();
            $form->currency('value', __('Giá trị'))->symbol('VND')->disable();
            $form->select('account_id', __('Số tài khoản'))->options($account)->required();
            $form->text('description', __('Mô tả'));
        }
        if ($form->isEditing()) {
            $id = request()->route()->parameter('tuition_collection');
            $recordStatus = $form->model()->find($id)->getOriginal("status");
            if ($recordStatus === 0) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
            } else if ($recordStatus === 1) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_UPDATE)->required();
            } else {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
            }
        } else {
            $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
        }

        $form->saved(function (Form $form) {
            $studentId = $form->model()->student_id;
            EduTuitionCollection::where('student_id', $studentId)
                ->where('id', '<>', $form->model()->id)
                ->where('status', 1)
                ->update(['status' => 3]);
        });

        $urlSchedule = 'https://business.metaverse-solution.vn/api/schedule';
        $urlScheduleById = 'https://business.metaverse-solution.vn/api/schedule/get-by-id';
        $urlClassById = 'https://business.metaverse-solution.vn/api/class/get-by-id';
        $urlStudent = 'https://business.metaverse-solution.vn/api/student';

        $script = <<<EOT
        $(function() {
            var unitPrice = $("#unit_price");
            var amount = $("#amount");
            var valueField = $("#value");
            var amountValue;
            
            function parseFormattedNumber(num) {
                return parseFloat(num.replace(/,/g, ''));
            };

            unitPrice.on('change', function() {
                var valueUnitPrice = parseFormattedNumber($(this).val());
                amount.on('change', function() {
                    amountValue = parseFormattedNumber($(this).val());
                    var valueTotal = valueUnitPrice * amountValue;
                    valueField.val(valueTotal);
                });
                valueField.val($(this).val() * amountValue);
            });

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
