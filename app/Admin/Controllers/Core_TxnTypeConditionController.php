<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Core\TransactionCode;
use App\Http\Models\Core\TxnTypeCondition;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Show;


class Core_TxnTypeConditionController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Điều kiện loại giao dịch';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $transactionCode = function ($transactionCodeId) {
            $transactionCode = TransactionCode::find($transactionCodeId);
            return $transactionCode ? $transactionCode->name : '';
        }; 

        $grid = new Grid(new TxnTypeCondition());
        
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('txn_code_credit', __('Mã giao dịch tín dụng'))->display($transactionCode)->filter('like');
        $grid->column('txn_code_debit', __('Mã giao dịch ghi nợ'))->display($transactionCode)->filter('like');
        $grid->column('txn_code_charge', __('Mã giao dịch môi giới'))->display($transactionCode)->filter('like');
        $grid->column('credit_max_date', __('Ngày tín dụng tối đa'));
        $grid->column('debit_max_date', __('Ngày ghi nợ tối đa'));
        $grid->column('status', __('Trạng thái'))->display($statusFormatter);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);

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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
        $transactionCode = function ($transactionCodeId) {
            $transactionCode = TransactionCode::find($transactionCodeId);
            return $transactionCode ? $transactionCode->name : '';
        }; 
        $show = new Show(TxnTypeCondition::findOrFail($id));

        $show->field('name', __('Tên'))->filter('like');
        $show->field('txn_code_credit', __('Mã giao dịch tín dụng'))->display($transactionCode);
        $show->field('txn_code_debit', __('Mã giao dịch ghi nợ'))->display($transactionCode);
        $show->field('txn_code_charge', __('Mã giao dịch môi giới'))->display($transactionCode);
        $show->field('credit_max_date', __('Ngày tín dụng tối đa'));
        $show->field('debit_max_date', __('Ngày ghi nợ tối đa'));
        $show->field('status', __('Trạng thái'))->as($statusFormatter);
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $transactionCodeCredits = TransactionCode::where('debit_credit_ind', 'C')->pluck('name', 'id')->toArray();
        $transactionCodeDebits = TransactionCode::where('debit_credit_ind', 'D')->pluck('name', 'id')->toArray();
        $transactionCodes = TransactionCode::all()->pluck('name', 'id');

        $form = new Form(new TxnTypeCondition());
        $form->text('name', __('Tên'))->required();
        $form->select('txn_code_credit', __('Mã giao dịch tín dụng'))->options($transactionCodeCredits);
        $form->select('txn_code_debit', __('Mã giao dịch ghi nợ'))->options($transactionCodeDebits);
        $form->select('txn_code_charge', __('Mã giao dịch môi giới'))->options($transactionCodes);
        $form->date('credit_max_date', __('Ngày tín dụng tối đa'));
        $form->date('debit_max_date', __('Ngày ghi nợ tối đa'));
        $form->select('status', __('Trạng thái'))->options($status)->required();
        return $form;
    }
}