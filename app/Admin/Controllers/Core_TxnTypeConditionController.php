<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\TransactionCode;
use App\Http\Models\Core\TxnTypeCondition;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


class Core_TxnTypeConditionController extends AdminController{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Loại giao dịch';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $transactionCode = function ($transactionCodeId) {
            $transactionCode = TransactionCode::find($transactionCodeId);
            return $transactionCode ? $transactionCode->name : '';
        }; 

        $grid = new Grid(new TxnTypeCondition());
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('txn_code_credit', __('Mã ghi có'))->display($transactionCode)->filter('like');
        $grid->column('txn_code_debit', __('Mã ghi nợ'))->display($transactionCode)->filter('like');
        $grid->column('txn_code_charge', __('Mã phí'))->display($transactionCode)->filter('like');
        $grid->column('credit_max_date', __('Ngày ghi có tối đa'));
        $grid->column('debit_max_date', __('Ngày ghi nợ tối đa'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "grid");
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return ConstantHelper::dateFormatter($updatedAt);
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
        $transactionCode = function ($transactionCodeId) {
            $transactionCode = TransactionCode::find($transactionCodeId);
            return $transactionCode ? $transactionCode->name : '';
        }; 
        $show = new Show(TxnTypeCondition::findOrFail($id));

        $show->field('name', __('Tên'))->filter('like');
        $show->field('txn_code_credit', __('Mã ghi có'))->display($transactionCode);
        $show->field('txn_code_debit', __('Mã ghi nợ'))->display($transactionCode);
        $show->field('txn_code_charge', __('Mã phí'))->display($transactionCode);
        $show->field('credit_max_date', __('Ngày ghi có tối đa'));
        $show->field('debit_max_date', __('Ngày ghi nợ tối đa'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "detail");
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
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();
        $transactionCodeCredits = TransactionCode::where('debit_credit_ind', 'C')->where("status", 1)->pluck('name', 'id')->toArray();
        $transactionCodeDebits = TransactionCode::where('debit_credit_ind', 'D')->where("status", 1)->pluck('name', 'id')->toArray();
        $transactionCodeCharges = TransactionCode::all()->where("status", 1)->pluck('name', 'id');

        $form = new Form(new TxnTypeCondition());
        $form->text('name', __('Tên'))->required();
        $form->select('txn_code_credit', __('Mã ghi có'))->options($transactionCodeCredits);
        $form->select('txn_code_debit', __('Mã ghi nợ'))->options($transactionCodeDebits);
        $form->select('txn_code_charge', __('Mã phí'))->options($transactionCodeCharges);
        $form->date('credit_max_date', __('Ngày ghi có tối đa'));
        $form->date('debit_max_date', __('Ngày ghi nợ tối đa'));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();
        return $form;
    }
}