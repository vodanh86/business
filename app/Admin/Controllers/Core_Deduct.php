<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Adjustment;
use App\Http\Models\Core\Deduct;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;

class Core_Deduct extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Khấu trừ';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new Deduct());
        $grid->column('trans_ref', __('Mã giao dịch'));
        $grid->column('branch.branch_name', __('Chi nhánh'));
        $grid->column('account_id', __('Tài khoản khấu trừ'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountGridFormatter($accountNumber);
        });
        $grid->column('amount', __('Số tiền khấu trừ'))->display(function ($amount) {
            return ConstantHelper::moneyFormatter($amount);
        });
        $grid->column('value_date', __('Ngày khấu trừ'))->display(function ($valueDate) {
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

        $show = new Show(Deduct::findOrFail($id));
        $show->field('trans_ref', __('Mã giao dịch'));
        $show->field('branch.branch_name', __('Chi nhánh'));
        $show->field('account_id', __('Tài khoản khấu trừ'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountDetailFormatter($accountNumber);
        });
        $show->field('amount', __('Số tiền khấu trừ'))->display(function ($amount) {
            return ConstantHelper::moneyFormatter($amount);
        });
        $show->field('value_date', __('Ngày khấu trừ'))->display(function ($valueDate) {
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

        $form = new Form(new Deduct());
        if ($form->isEditing()) {
            $id = request()->route()->parameter("deduct");
            $model = $form->model()->find($id);
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $accountId = $form->model()->find($id)->getOriginal("account_id");
            $status = $model->status;

            $form->text("trans_ref", __('Mã giao dịch'))->readonly();
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId)->readonly();
            $form->select('account_id', __('Tài khoản khấu trừ'))->options($bankAccountOptions)->default($accountId)->readonly();
            $form->currency('amount', __('Số tiền khấu trừ'))->symbol('VND')->readonly();
            $form->date('value_date', __('Ngày khấu trừ'))->readonly();
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
            $tranferId = (new UtilsCommonHelper)->generateTransactionId("DD");
            $form->text("trans_ref", __('Mã giao dịch'))->default($tranferId)->readonly();
            $form->hidden('trans_ref')->value($tranferId);
            $form->hidden('business_id')->value($business->id);
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('account_id', __('Tài khoản khấu trừ'))->options($bankAccountOptions);
            $form->currency('amount', __('Số tiền khấu trừ'))->symbol('VND');
            $form->date('value_date', __('Ngày khấu trừ'));
            $form->textarea('description', __('Mô tả'));
            $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
        }
        return $form;
    }
}
