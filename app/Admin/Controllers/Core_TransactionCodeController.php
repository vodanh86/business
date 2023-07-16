<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Core\TransactionCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Show;


class Core_TransactionCodeController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Mã Giao dịch';

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

        $grid = new Grid(new TransactionCode());
        
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('debit_credit_ind', __('Ghi nợ tín dụng ind'))->filter('like');
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
       
        $show = new Show(TransactionCode::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('debit_credit_ind', __('Ghi nợ tín dụng ind'));
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
        
        $form = new Form(new TransactionCode());
        $form->hidden('business_id')->value($business->id);
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();
        $form->text('name', __('Tên'));
        $form->text('debit_credit_ind', __('Ghi nợ tín dụng ind'));
        $form->select('status', __('Trạng thái'))->options($status)->required();

        return $form;
    }
}