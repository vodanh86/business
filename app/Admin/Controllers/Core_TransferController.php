<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Transfer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;

class Core_TransferController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chuyển tiền';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new Transfer());
        $grid->column('trans_ref', __('Mã giao dịch'));
        $grid->column('branch.branch_name', __('Chi nhánh'));
        $grid->column('debit_acct_id', __('Tài khoản chuyển tiền'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountGridFormatter($accountNumber);
        });
        $grid->column('credit_acct_id', __('Tài khoản nhận tiền'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountGridFormatter($accountNumber);
        });
        $grid->column('amount', __('Số tiền'))->display(function ($money) {
            return ConstantHelper::moneyFormatter($money);
        });
        $grid->column('description', __('Mô tả'));
        $grid->column('status', __('Trạng thái'))->display(function ($value) {
            return Constant::RECORD_STATUS[$value] ?? '';
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

        $show = new Show(Transfer::findOrFail($id));
        $show->field('trans_ref', __('Mã giao dịch'));
        $show->field('branch.branch_name', __('Chi nhánh'));
        $show->field('debit_acct_id', __('Tài khoản chuyển tiền'))->as(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountDetailFormatter($accountNumber);
        });
        $show->field('credit_acct_id', __('Tài khoản nhận tiền'))->as(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountDetailFormatter($accountNumber);
        });
        $show->field('amount', __('Số tiền'))->as(function ($amount) {
            return ConstantHelper::moneyFormatter($amount);
        });
        $show->field('description', __('Mô tả'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return ConstantHelper::transactionRecordStatus($status);
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

        $form = new Form(new Transfer());
        if ($form->isEditing()) {
            $id = request()->route()->parameter("transfer");
            $model = $form->model()->find($id);
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $creditAccId = $form->model()->find($id)->getOriginal("credit_acct_id");
            $debitAccId = $form->model()->find($id)->getOriginal("debit_acct_id");
            $status = $model->status;

            $form->text("trans_ref", __('Mã giao dịch'))->readonly();
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId)->readonly();
            $form->select('credit_acct_id', __('Tài khoản chuyển tiền'))->options($bankAccountOptions)->default($creditAccId)->readonly();
            $form->select('debit_acct_id', __('Tài khoản nhận tiền'))->options($bankAccountOptions)->default($debitAccId)->readonly();
            $form->currency('amount', __('Số tiền'))->symbol('VND')->readonly();
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
            $tranferId = (new UtilsCommonHelper)->generateTransactionId("FT");
            $form->text("trans_ref", __('Mã giao dịch'))->default($tranferId)->readonly();
            $form->hidden('trans_ref')->value($tranferId);
            $form->hidden('business_id')->value($business->id);
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('credit_acct_id', __('Tài khoản chuyển tiền'))->options($bankAccountOptions);
            $form->select('debit_acct_id', __('Tài khoản nhận tiền'))->options($bankAccountOptions);
            $form->currency('amount', __('Số tiền'))->symbol('VND');
            $form->date('value_date', __('Ngày chuyển'));
            $form->textarea('description', __('Mô tả'));
            $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
        }
        return $form;
    }
}
