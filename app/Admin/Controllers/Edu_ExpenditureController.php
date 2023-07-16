<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Core\Expense;
use App\Http\Models\Edu\EduClass;
use App\Http\Models\Edu\EduExpenditure;
use App\Http\Models\Edu\EduTeacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_ExpenditureController extends AdminController{
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $moneyFormatter = function($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new EduExpenditure());
        
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('expense.name', __('Chi phí'));
        $grid->column('class.name', __('Tên lớp học'));
        $grid->column('account.number', __('Số tài khoản'));
        $grid->column('account.bank_name', __('Tên ngân hàng'));
        $grid->column('amount', __('Số tiền'))->display($moneyFormatter);
        $grid->column('value_date', __('Ngày đóng'))->display($dateFormatter);
        $grid->column('description', __('Mô tả'));
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
        $moneyFormatter = function($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };

        $show = new Show(EduExpenditure::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('expense.name', __('Chi phí'));
        $show->field('class.name', __('Tên lớp học'));
        $show->field('account.number', __('Số tài khoản'));
        $show->field('account.bank_name', __('Tên ngân hàng'));
        $show->field('amount', __('Số tiền'))->as($moneyFormatter);
        $show->field('value_date', __('Ngày đóng'))->as($dateFormatter);
        $show->field('description', __('Mô tả'));
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
        $bankName = Account::where('business_id', Admin::user()->business_id)->where('status', 1)->pluck("number", "id");
        $expense = Expense::where('business_id', Admin::user()->business_id)->where('status', 1)->pluck("name", "id");
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
       
        $form = new Form(new EduExpenditure());
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
        $form->select('account_id', __('Số Tài khoản'))->options($bankName)->required();
        $form->select('expense_id', __('Chi phí'))->options($expense)->required();
        
        $form->divider('2. Thông tin chi tiêu');
        $form->currency('amount', __('Số tiền'))->symbol('VND');
        $form->date('value_date', __('Ngày đóng'));
        $form->text('description', __('Mô tả'));
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