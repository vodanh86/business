<?php

namespace App\Admin\Controllers;

use App\Http\Models\AdminUser;
use App\Http\Models\Core\Account;
use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Core\Transfer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
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

        $moneyFormatter = function ($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $formatBankAccount = function ($value) {
            $bankAccount = Account::where('id', $value)
                ->first();
            if ($bankAccount) {
                return "<span class='label label-primary'>$bankAccount->bank_name - $bankAccount->number</span>";
            }
            return '';
        };

        $grid = new Grid(new Transfer());


        $grid->column('branch.branch_name', __('Chi nhánh'));
        $grid->column('debit_acct_id', __('Tài khoản chuyển tiền'))->display($formatBankAccount);
        $grid->column('credit_acct_id', __('Tài khoản nhận tiền'))->display($formatBankAccount);;
        $grid->column('amount', __('Số tiền'))->display($moneyFormatter);
        $grid->column('description', __('Mô tả'));
        $grid->column('status', __('Trạng thái'))->display(function ($value) {
            return Constant::RECORD_STATUS[$value] ?? '';
        });
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
        $grid->model()->where('business_id', '=', Admin::user()->business_id);

        $grid->actions(function ($actions) {
            $status = $actions->row->status;
            if ($status === 0) {
            } else if ($status === 1) {
                $actions->disableDelete();
            } else {
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
        $recordStatus = function ($value) {
            if (array_key_exists($value, Constant::RECORD_STATUS)) {
                return Constant::RECORD_STATUS[$value];
            } else {
                return '';
            }
        };
        $moneyFormatter = function ($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };

        $show = new Show(Transfer::findOrFail($id));
        $show->field('branch.branch_name', __('Chi nhánh'));
        $show->field('debitAccount.number', __('Tài khoản chuyển tiền'));
        $show->field('debitAccount.bank_name', __('Ngân hàng chuyển tiền'));
        $show->field('creditAccount.number', __('Tài khoản nhận tiền'));
        $show->field('creditAccount.bank_name', __('Ngân hàng nhận tiền'));
        $show->field('amount', __('Số tiền'))->as($moneyFormatter);
        $show->field('description', __('Mô tả'));
        $show->field('status', __('Trạng thái'))->as($recordStatus);
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
        $business = Business::where('id', Admin::user()->business_id)->first();
        $branchesBiz = Branch::where('business_id', Admin::user()->business_id)->pluck('branch_name', 'id');
        $bankAccounts = Account::where('business_id', Admin::user()->business_id)->where('status', 1)->get();
        $bankAccountOptions = $bankAccounts->map(function ($account) {
            return [
                'value' => $account->id,
                'text' => $account->bank_name . ' - ' . $account->number,
            ];
        })->pluck('text', 'value');

        $form = new Form(new Transfer());

        if ($form->isEditing()) {
            $id = request()->route()->parameter("transfer");
            $model = $form->model()->find($id);
            $status = $model->status;
            $branchName = $model->branch->branch_name;
            $form->text('branch_id', __('Tên chi nhánh'))->value($branchName)->disable();
            $form->text('credit_acct_id', __('Tài khoản chuyển tiền'))->value($model->credit_acct_id)->disable();
            $form->text('debit_acct_id', __('Tài khoản nhận tiền'))->value($model->debit_acct_id)->disable();
            $form->currency('amount', __('Số tiền'))->symbol('VND')->disable();
            $form->date('value_date', __('Ngày chuyển'))->disable();
            $form->text('description', __('Mô tả'))->disable();
            if ($status === 0) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_INSERT_AND_UPDATE)->required();
            } else if ($status === 1) {
                $form->select('status', __('Trạng thái'))->options(Constant::RECORDSTATUS_UPDATE)->required();
            }
        } else {
            $form->hidden('business_id')->value($business->id);
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->required();
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
