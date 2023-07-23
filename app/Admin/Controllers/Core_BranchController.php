<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\Branch;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


class Core_BranchController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chi nhánh';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Branch());

        $grid->column('branch_name', __('Tên chi nhánh'));
        $grid->column('address', __('Địa chỉ'));
        $grid->column('phone', __('Số điện thoại'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "grid");
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->model()->where('business_id', '=', Admin::user()->business_id);
        $grid->fixColumns(0,0);
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
        $show = new Show(Branch::findOrFail($id));

        $show->field('branch_name', __('Tên chi nhánh'));
        $show->field('address', __('Địa chỉ'));
        $show->field('phone', __('Số điện thoại'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusFormatter($status,"Core", "detail");
        });
        $show->field('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $show->field('updated_at', __('Ngày cập nhật'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
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

        $form = new Form(new Branch());
        $form->hidden('business_id')->value($business->id);
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();
        $form->text('branch_name', __('Tên chi nhánh'))->required();
        $form->text('address', __('Địa chỉ'))->required();
        $form->mobile('phone', __('Số điện thoại'))->options(['mask' => '999 999 9999'])->required();
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();

        $url = env('APP_URL') . '/api/business';
        $script = <<<EOT
        $(function() {
            var businessId = $(".business_id").val();
            $.get("$url",{q : businessId}, function (data) {
                $("#name_business").val(data.name);  
            });
        });
        EOT;
        Admin::script($script);

        return $form;
    }
}
