<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduClass;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;

class Edu_ClassController extends AdminController{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Lớp học';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduClass());
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('name', __('Tên lớp'));
        $grid->column('description', __('Mô tả'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusGridFormatter($status);
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
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
        $show = new Show(EduClass::findOrFail($id));
        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('name', __('Tên lớp'));
        $show->field('description', __('Mô tả'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusDetailFormatter($status);
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
        $business = (new UtilsCommonHelper)->currentBusiness();
        $branch = (new UtilsCommonHelper)->optionsBranch();
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();

        $form = new Form(new EduClass());
        $form->hidden('business_id')->value($business->id);
        $form->select('branch_id', __('Tên chi nhánh'))->options($branch)->required();
        $form->text('name', __('Tên lớp'));
        $form->text('description', __('Mô tả'));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();
        return $form;
    }
}