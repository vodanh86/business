<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\CommonCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


class Core_CommonCodeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tham số hệ thống';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new CommonCode());

        $grid->column('business.name', __('Tên doanh nghiệp'));
        $grid->column('group', __('Nhóm'))->filter('like');
        $grid->column('type', __('Thể loại'))->filter('like');
        $grid->column('value', __('Giá trị'));
        $grid->column('description_vi', __('Mô tả tiếng việt'));
        $grid->column('order', __('Sắp xếp'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "grid");
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return ConstantHelper::dateFormatter($updatedAt);
        });
        $grid->model()->where('business_id', '=', Admin::user()->business_id)->orderByDesc("id")->orderBy("order");
        $grid->disableExport();

        $grid->actions(function ($actions) {
            $blockDelete = $actions->row->block_delete;
            if ($blockDelete === 1) {
                $actions->disableDelete();
            }
        });

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
        $show = new Show(CommonCode::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('group', __('Nhóm'));
        $show->field('type', __('Thể loại'));
        $show->field('value', __('Giá trị'));
        $show->field('description_vi', __('Mô tả tiếng việt'));
        $show->field('order', __('Sắp xếp'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "detail");
        });
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));

        $blockDelete = $show->getModel()->getOriginal("block_delete");
        if ($blockDelete === 1) {
            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableDelete();
                });;
        }

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
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();

        $form = new Form(new CommonCode());
        $form->hidden('business_id')->value($business->id);
        if ($form->isEditing()) {
            $form->tools(function (Form\Tools $tools) {
                $tools->disableDelete();
            });
            $form->text('group', __('Nhóm'))->disable();
            $form->text('type', __('Thể loại'))->disable();
            $form->text('value', __('Giá trị'))->disable();
        } else {
            $form->text('group', __('Nhóm'))->help('Yêu cầu tên mã nhóm - Ví dụ(Edu)')->required();
            $form->text('type', __('Thể loại'))->help('Yêu cầu tên thể loại - Ví dụ(School)')->required();
            $form->text('value', __('Giá trị'))->help('Yêu cầu giá trị - Ví dụ(1: VIN, 2:FPT)')->required();
        }
        $form->text('description_vi', __('Mô tả tiếng việt'))->required();
        $form->text('description_en', __('Mô tả tiếng anh'))->required();
        $form->text('order', __('Sắp xếp'));
        $form->select('block_delete', __('Chặn xoá'))->options(array(0 => 'Không', 1 => 'Có'))->required();
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
