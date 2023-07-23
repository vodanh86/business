<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Edu\EduClass;
use App\Http\Models\Edu\EduSchedule;
use App\Http\Models\Edu\EduStudent;
use App\Http\Models\Edu\EduTuitionCollection;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_TuitionCollectionController extends AdminController{
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
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $moneyFormatter = function ($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };
        $recordStatus = function ($value) {
            if (array_key_exists($value, Constant::RECORD_STATUS)) {
                return Constant::RECORD_STATUS[$value];
            } else {
                return '';
            }
        };
        $grid = new Grid(new EduTuitionCollection());
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Lịch học'));
        $grid->column('student.name', __('Học sinh'));
        $grid->column('processing_date', __('Ngày nghiệm thu'));
        $grid->column('value_date', __('Ngày nộp tiền'))->display($dateFormatter);
        $grid->column('amount', __('Số lượng'));
        $grid->column('unit_price', __('Đơn giá'))->display($moneyFormatter);
        $grid->column('value', __('Giá trị'))->display($moneyFormatter);
        $grid->column('next_date', __('Ngày tiếp theo'))->display($dateFormatter);
        $grid->column('account.number', __('Số tài khoản'));
        $grid->column('description', __('Mô tả'));
        $grid->column('record_status', __('Trạng thái'))->display($recordStatus);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
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
        $moneyFormatter = function ($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $recordStatus = function ($value) {
            if (array_key_exists($value, Constant::RECORD_STATUS)) {
                return Constant::RECORD_STATUS[$value];
            } else {
                return '';
            }
        };

        $show = new Show(EduTuitionCollection::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('schedule.name', __('Lịch học'));
        $show->field('student.name', __('Học sinh'));
        $show->field('processing_date', __('Ngày nghiệm thu'));
        $show->field('value_date', __('Ngày nộp tiền'))->as($dateFormatter);
        $show->field('amount', __('Số lượng'));
        $show->field('unit_price', __('Đơn giá'))->as($moneyFormatter);
        $show->field('value', __('Giá trị'))->as($moneyFormatter);
        $show->field('next_date', __('Ngày tiếp theo'))->as($dateFormatter);
        $show->field('account_id', __('Số tài khoản'));
        $show->field('description', __('Mô tả'))->width(150);
        $show->field('record_status', __('Trạng thái'))->as($$recordStatus);
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
        $recordStatus = function ($value) {
            if (array_key_exists($value, Constant::RECORD_STATUS)) {
                return Constant::RECORD_STATUS[$value];
            } else {
                return '';
            }
        };

        $business = Business::where('id', Admin::user()->business_id)->first();
        $account = Account::where('business_id', Admin::user()->business_id)->pluck('number', 'id');
        $branchesBiz = Branch::where('business_id', Admin::user()->business_id)->pluck('branch_name', 'id');
        $allClass= EduClass::all()->pluck('name', 'id');
        $allStudent= EduStudent::all()->pluck('name', 'id');
        $allSchedule = EduSchedule::all()->pluck('name', 'id');


        $form = new Form(new EduTuitionCollection());
        $form->divider('1. Thông tin cơ bản');
        $form->hidden('business_id')->value($business->id);

        if ($form->isEditing()) {

            $id = request()->route()->parameter('tuition_collection');
            $model = $form->model()->find($id);
            // dd($model);

            $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options($allSchedule)->default(function ($id) {
                $schedule = EduSchedule::find($id);
                if ($schedule) {
                    return [$schedule->id => $schedule->name];
                }
            });
            $form->select('student_id', __('Tên học sinh'))->options($allStudent)->default(function ($id) {
                $student = EduStudent::find($id);
                if ($student) {
                    return [$student->id => $student->name];
                }
            });
        }else{
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->required();
            $form->select('schedule_id', __('Tên lịch học'))->options()->required();
            $form->text('class_name', __('Tên lớp học'))->disable()->required();
            $form->select('student_id', __('Tên học sinh'))->options()->required();
        }

        $form->divider('2. Thông tin thu học phí');
        $form->date('processing_date', __('Ngày đóng tiền'))->required();
        $form->date('value_date', __('Ngày bắt đầu học'))->required();
        $form->currency('unit_price', __('Đơn giá'))->symbol('VND')->required();
        $form->text('amount', __('Số buổi'))->required();
        $form->currency('value', __('Giá trị'))->symbol('VND')->disable();
        $form->select('account_id', __('Số tài khoản'))->options($account)->required();
        $form->text('description', __('Mô tả'));

        if ($form->isEditing()) {
            $recordStatus = $form->model()->record_status;
            if ($recordStatus === 0) {
                $form->select('record_status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
            } else if ($recordStatus === 1) {
                $form->select('record_status', __('Trạng thái'))->options(Constant::RECORDSTATUS_UPDATE)->required();
            } else {
                $form->select('record_status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
            }
        } else {
            $form->select('record_status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
        }
        

        $urlSchedule = 'https://business.metaverse-solution.vn/api/schedule';
        $urlScheduleById = 'https://business.metaverse-solution.vn/api/schedule/get-by-id';
        $urlClassById = 'https://business.metaverse-solution.vn/api/class/get-by-id';
        $urlStudent = 'https://business.metaverse-solution.vn/api/student';

        $script = <<<EOT
        $(function() {
            var unitPrice = $("#unit_price");
            var amount = $("#amount");
            var valueField = $("#value");
            
            unitPrice.on('change', function() {
                var valueUnitPrice = parseFloat($(this).val());
                amount.on('change', function() {
                    var amountValue = parseFloat($(this).val());
                    var valueTotal = valueUnitPrice * amountValue;
                    var formattedValue = valueTotal.toLocaleString(undefined, {minimumFractionDigits: 3});
                    valueField.val(formattedValue);
                });
            });

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

                    $.get("$urlStudent", { class_id: schedule.class_id }, function (students) {
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