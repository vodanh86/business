<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduEmployee;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class Edu_EmployeeController extends AdminController{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Nhân sự';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduEmployee());
        
        $grid->column('name', __('Họ và tên'));
        $grid->column('phone', __('Số điện thoại'));
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
        $grid->fixColumns(0, 0);
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
        $show = new Show(EduEmployee::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('name', __('Họ và tên'));
        $show->field('phone', __('Số điện thoại'));
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
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();
        $business = (new UtilsCommonHelper)->currentBusiness();

        $form = new Form(new EduEmployee());
        $form->hidden('business_id')->value($business->id);
        $form->text('name', __('Họ và tên'));
        $form->mobile('phone', __('Số điện thoại'))->options(['mask' => '999 999 9999'])->required();
        $form->select('status', __('Trạng thái'))->options($statusOptions)->required($statusDefault);

        return $form;
    }
}