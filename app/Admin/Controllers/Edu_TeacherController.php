<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduTeacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_TeacherController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Giảng viên';

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
        $grid = new Grid(new EduTeacher());
        
        $grid->column('name', __('Họ và tên'));
        $grid->column('phone', __('Số điện thoại'));
        $grid->column('status', __('Trạng thái'))->display(function ($value) use ($status) {
            return $status[$value] ?? '';
        });
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $show = new Show(EduTeacher::findOrFail($id));

        $show->field('name', __('Họ và tên'));
        $show->field('phone', __('Số điện thoại'));
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
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $business = Business::where('id', Admin::user()->business_id)->first();
        $form = new Form(new EduTeacher());
        $form->hidden('business_id')->value($business->id);
        $form->text('name', __('Họ và tên'));
        $form->mobile('phone', __('Số điện thoại'))->options(['mask' => '999 999 9999'])->required();
        $form->select('status', __('Trạng thái'))->options($status)->required();

        return $form;
    }
}