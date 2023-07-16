<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Core\Expense;
use App\Http\Models\Core\ExpenseGroup;
use App\Http\Models\Core\ExpenseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Show;


class Core_ExpenseController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chi phí';

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

        $grid = new Grid(new Expense());

        $grid->column('groupExpense.name', __('Nhóm chi phí'));
        $grid->column('typeExpense.name', __('Loại chi phí'));
        $grid->column('name', __('Tên'))->filter('like');
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
       
        $show = new Show(Expense::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('groupExpense.name', __('Nhóm chi phí'));
        $show->field('typeExpense.name', __('Loại chi phí'));
        $show->field('name', __('Tên'));
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
        $groupExpense = ExpenseGroup::all()->pluck("name","id");
        $typeExpense = ExpenseType::all()->pluck("name","id");

        $form = new Form(new Expense());
        $form->hidden('business_id')->value($business->id);
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();
        $form->select('group_id', __('Nhóm chi phí'))->options($groupExpense)->required();
        $form->select('type_id', __('Loại chi phí'))->options($typeExpense)->required();
        $form->text('name', __('Tên'));
        $form->select('status', __('Trạng thái'))->options($status)->required();

        return $form;
    }
}