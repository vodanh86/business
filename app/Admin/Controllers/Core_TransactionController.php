<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Core\Transaction;
use App\Http\Models\Core\TxnTypeCondition;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Show;


class Core_TransactionController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Giao dịch';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Transaction());
        
        $grid->column('tnx_code', __('Mã giao dịch'))->filter('like');
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('txnType.name', __('Loại giao dịch'))->filter('like');
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "grid");
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return ConstantHelper::dateFormatter($updatedAt);
        });
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
        $show = new Show(Transaction::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('tnx_code', __('Mã giao dịch'));
        $show->field('name', __('Tên'));
        $show->field('txnType.name', __('Loại giao dịch'));
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
        $business = (new UtilsCommonHelper)->currentBusiness();
        $txnType = TxnTypeCondition::all()->where("status", 1)->pluck("name","id");
        
        $form = new Form(new Transaction());
        $form->hidden('business_id')->value($business->id);
        $form->text('tnx_code', __('Mã giao dịch'))->required();
        $form->text('name', __('Tên'))->required();
        $form->select('txn_type_id', __('Loại giao dịch'))->options($txnType)->required();
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();
        
        return $form;
    }
}