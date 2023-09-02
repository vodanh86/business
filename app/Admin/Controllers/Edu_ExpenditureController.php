<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Expense;
use App\Http\Models\Edu\EduClass;
use App\Http\Models\Edu\EduExpenditure;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_ExpenditureController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chi tiêu';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduExpenditure());

        $grid->column('trans_ref', __('Mã giao dịch'));
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('expense.name', __('Chi phí'));
        $grid->column('class_id', __('Tên lớp học'))->display(function ($classId) {
            if ($classId == 0) {
                return 'Toàn bộ';
            } else {
                $className = EduClass::where("id", $classId)->pluck("name")->first();
                return $className;
            }
        });
        $grid->column('account_id', __('Số tài khoản'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountGridFormatter($accountNumber);
        });
        $grid->column('amount', __('Số tiền'))->display(function ($money) {
            return ConstantHelper::moneyFormatter($money);
        });
        $grid->column('value_date', __('Ngày chi tiêu'))->display(function ($valueDate) {
            return ConstantHelper::dateFormatter($valueDate);
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

        $show = new Show(EduExpenditure::findOrFail($id));

        $show->field('trans_ref', __('Mã giao dịch'));
        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('expense.name', __('Chi phí'));
        $show->field('class.name', __('Tên lớp học'));
        $show->field('account_id', __('Tài khoản'))->as(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountDetailFormatter($accountNumber);
        });
        $show->field('amount', __('Số tiền'))->as(function ($amount) {
            return ConstantHelper::moneyFormatter($amount);
        });
        $show->field('value_date', __('Ngày chi tiêu'));
        $show->field('description', __('Mô tả'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return ConstantHelper::transactionDetailRecordStatus($status);
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
        $bankAccountOptions = (new UtilsCommonHelper)->bankAccountFormFormatter();
        $expense = Expense::where('business_id', Admin::user()->business_id)->where('status', 1)->pluck("name", "id");

        $form = new Form(new EduExpenditure());
        if ($form->isEditing()) {
            $id = request()->route()->parameter("expenditure");
            $model = $form->model()->find($id);
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $classes = (new UtilsCommonHelper)->optionsClassByBranchId($branchId);
            $status = $model->status;

            $form->text("trans_ref", __('Mã giao dịch'))->readonly();
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId)->readonly();
            $form->select('class_id', __('Tên lớp học'))->options($classes)->default($model->getOriginal("class_id"))->readonly();
            $form->select('account_id', __('Số Tài khoản'))->options($bankAccountOptions)->readonly();
            $form->select('expense_id', __('Chi phí'))->options($expense)->readonly();
            $form->currency('amount', __('Số tiền'))->symbol('VND')->readonly();
            $form->date('value_date', __('Ngày chi tiêu'))->readonly();
            $form->textarea('description', __('Mô tả'))->readonly();
            if ($status === 0) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
            } else if ($status === 1) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_UPDATE)->required();
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableDelete();
                });
            }
        } else {
            $tranferId = (new UtilsCommonHelper)->generateTransactionId("EX");
            $form->hidden('trans_ref')->value($tranferId);
            $form->text("trans_ref", __('Mã giao dịch'))->default($tranferId)->readonly();
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('class_id', __('Tên lớp học'))->options()->required();
            $form->hidden('business_id')->value($business->id);
            $form->select('account_id', __('Số Tài khoản'))->options($bankAccountOptions)->required();
            $form->select('expense_id', __('Chi phí'))->options($expense)->required();
            $form->currency('amount', __('Số tiền'))->symbol('VND');
            $form->date('value_date', __('Ngày đóng'));
            $form->textarea('description', __('Mô tả'));
            $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
        }


        $url = env('APP_URL') . '/api/class';
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
                $.get("$url", { branch_id: selectedBranchId }, function (classArr) {
                    console.log(classArr)
                    var classesActive = classArr.filter(function (cls) {
                        return cls.status === 1;
                    });                    
                    $.each(classesActive, function (index, cls) {
                        optionsClass[cls.id] = cls.name;
                    });
                    classSelect.empty();
                    classSelect.append($('<option>', {
                        value: '0',
                        text: 'Toàn bộ'
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
