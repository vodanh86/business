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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };

        $grid = new Grid(new Transaction());
        
        $grid->column('tnx_code', __('Mã giao dịch'))->filter('like');
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('txnType.name', __('Loại giao dịch'))->filter('like');
        $grid->column('status', __('Trạng thái'))->display($statusFormatter);
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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
       
        $show = new Show(Transaction::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('tnx_code', __('Mã giao dịch'));
        $show->field('name', __('Tên'));
        $show->field('txnType.name', __('Loại giao dịch'));
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
        $business = Business::where('id', Admin::user()->business_id)->first();
        $txnType = TxnTypeCondition::all()->pluck("name","id");
        
        $form = new Form(new Transaction());
        $form->hidden('business_id')->value($business->id);
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();
        $form->text('tnx_code', __('Mã TNX'))->required();
        $form->text('name', __('Tên'))->required();
        $form->select('txn_type_id', __('txn_type_id'))->options($txnType)->required();
        $form->select('status', __('Trạng thái'))->options($status)->required();
        return $form;
    }
}