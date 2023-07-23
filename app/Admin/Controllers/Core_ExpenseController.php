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
        $grid = new Grid(new Expense());

        $grid->column('groupExpense.name', __('Nhóm chi phí'));
        $grid->column('typeExpense.name', __('Loại chi phí'));
        $grid->column('name', __('Tên'))->filter('like');
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
        $grid->disableExport();

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
        $show = new Show(Expense::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('groupExpense.name', __('Nhóm chi phí'));
        $show->field('typeExpense.name', __('Loại chi phí'));
        $show->field('name', __('Tên'));
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
        $groupExpense = ExpenseGroup::all()->where("status",1)->pluck("name","id");
        $typeExpense = ExpenseType::all()->where("status",1)->pluck("name","id");

        $form = new Form(new Expense());
        $form->hidden('business_id')->value($business->id);
        $form->select('group_id', __('Nhóm chi phí'))->options($groupExpense)->required();
        $form->select('type_id', __('Loại chi phí'))->options($typeExpense)->required();
        $form->text('name', __('Tên'));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();


        return $form;
    }
}