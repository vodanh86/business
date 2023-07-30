<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Adjustment;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;

class Core_Adjustment extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Điều chỉnh';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new Adjustment());
        $grid->column('trans_ref', __('Mã giao dịch'));
        $grid->column('branch.branch_name', __('Chi nhánh'));
        $grid->column('account_id', __('Tài khoản chuyển tiền'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountGridFormatter($accountNumber);
        });
        $grid->column('old_balance', __('Số dư trước'))->display(function ($oldBalance) {
            return ConstantHelper::moneyFormatter($oldBalance);
        });
        $grid->column('new_balance', __('Số dư sau'))->display(function ($newBalance) {
            return ConstantHelper::moneyFormatter($newBalance);
        });
        $grid->column('amount', __('Số tiền'))->display(function ($amount) {
            return ConstantHelper::moneyFormatter($amount);
        });
        $grid->column('value_date', __('Ngày chuyển'))->display(function ($valueDate) {
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
        $grid->actions(function ($actions) {
            $status = $actions->row->status;
            if ($status === 1) {
                $actions->disableDelete();
            } else if ($status === 2) {
                $actions->disableEdit();
                $actions->disableDelete();
            }
        });
        $grid->fixColumns(0,0);

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

        $show = new Show(Adjustment::findOrFail($id));
        $show->field('trans_ref', __('Mã giao dịch'));
        $show->field('branch.branch_name', __('Chi nhánh'));
        $show->field('account_id', __('Tài khoản chuyển tiền'))->as(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountDetailFormatter($accountNumber);
        });
        $show->field('old_balance', __('Số dư trước'))->as(function ($oldBalance) {
            return ConstantHelper::moneyFormatter($oldBalance);
        });
        $show->field('new_balance', __('Số dư sau'))->as(function ($newBalance) {
            return ConstantHelper::moneyFormatter($newBalance);
        });
        $show->field('amount', __('Số tiền điều chỉnh'))->as(function ($amount) {
            return ConstantHelper::moneyFormatter($amount);
        });
        $show->field('value_date', __('Ngày chuyển'))->as(function ($valueDate) {
            return ConstantHelper::dateFormatter($valueDate);
        });
        $show->field('description', __('Mô tả'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return ConstantHelper::transactionDetailRecordStatus($status);
        });
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
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
        $bankAccountOptions = (new UtilsCommonHelper)->bankAccountFormFormatter();

        $form = new Form(new Adjustment());
        if ($form->isEditing()) {
            $id = request()->route()->parameter("adjustment");
            $model = $form->model()->find($id);
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $accountId = $form->model()->find($id)->getOriginal("account_id");
            $status = $model->status;

            $form->text("trans_ref", __('Mã giao dịch'))->readonly();
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId)->readonly();
            $form->select('account_id', __('Tài khoản chuyển tiền'))->options($bankAccountOptions)->default($accountId)->readonly();
            $form->currency('old_balance', __('Số dư trước'))->symbol('VND')->readonly();
            $form->currency('new_balance', __('Số dư sau'))->symbol('VND')->readonly();
            $form->currency('amount', __('Số tiền điều chỉnh'))->symbol('VND')->readonly();
            $form->date('value_date', __('Ngày chuyển'))->readonly();
            $form->text('description', __('Mô tả'))->readonly();
            if ($status === 0) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
            } else if ($status === 1) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_UPDATE)->required();
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableDelete();
                });
            }
        } else {
            $tranferId = (new UtilsCommonHelper)->generateTransactionId("AJ");
            $form->text("trans_ref", __('Mã giao dịch'))->default($tranferId)->readonly();
            $form->hidden('trans_ref')->value($tranferId);
            $form->hidden('business_id')->value($business->id);
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('account_id', __('Tài khoản chuyển tiền'))->options($bankAccountOptions);
            $form->currency('old_balance', __('Số dư trước'))->symbol('VND')->readonly();
            $form->currency('new_balance', __('Số dư sau'))->symbol('VND');
            $form->currency('amount', __('Số tiền điều chỉnh'))->symbol('VND')->readonly();
            $form->date('value_date', __('Ngày chuyển'));
            $form->textarea('description', __('Mô tả'));
            $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
        }

        $url = env('APP_URL') . '/api/account';
        $script = <<<EOT
        $(function() {
            var amount =  $(".amount");
            var accountId = $(".account_id");
            var oldBalance = $(".old_balance");
            var newBalance = $(".new_balance");

            function parseFormattedNumber(num) {
                return parseFloat(num.replace(/,/g, ''));
            };

            accountId.on('change', function() {
                $.get("$url", { q: $(this).val() }, function (account) {
                    
                    newBalance.val(account.balance);
                    oldBalance.val(account.balance)

                    newBalance.on('change', function() {
                        var valueOldBalance = parseFormattedNumber(oldBalance.val());
                        if($(this).val()){
                            var valueNewBalance = parseFormattedNumber($(this).val());
                        }else{
                            var valueNewBalance = 0;
                        }
                        var valueAmount = valueNewBalance - valueOldBalance;
                        amount.val(valueAmount);
                    });
                });
            });
        });

        EOT;
        Admin::script($script);
        return $form;
    }
}
