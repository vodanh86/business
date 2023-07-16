<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Show;

class Core_AccountController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tài khoản';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new Account());
        
        $grid->column('number', __('Số tài khoản'));
        $grid->column('type', __('Loại'));
        $grid->column('bank_name', __('Tên ngân hàng'));
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
        $show = new Show(Account::findOrFail($id));

        $show->field('number', __('Số tài khoản'));
        $show->field('type', __('Loại'));
        $show->field('bank_name', __('Tên ngân hàng'));
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');

        $form = new Form(new Account());
        $form->hidden('business_id')->value($business->id);
        $form->text('number', __('Số tài khoản'));
        $form->select('type', __('Loại'))->options(array("Asset" => "Asset", "Liabilities"));
        $form->text('bank_name', __('Tên ngân hàng'));
        $form->select('status', __('Trạng thái'))->options($status)->required();

        return $form;
    }
}