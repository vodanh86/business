<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Show;


class Core_CommonCodeController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Common Code';

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

        $grid = new Grid(new CommonCode());
        
        $grid->column('id', __('ID'));
        $grid->column('business.name', __('Tên doanh nghiệp'));
        $grid->column('group', __('Nhóm'))->filter('like');
        $grid->column('type', __('Thể loại'))->filter('like');
        $grid->column('value', __('Giá trị'));
        $grid->column('description_vi', __('Mô tả tiếng việt'));
        $grid->column('order', __('Sắp xếp'));
        $grid->column('status', __('Trạng thái'))->display($statusFormatter);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
        $grid->model()->where('business_id', '=', Admin::user()->business_id)->orderByDesc("id")->orderBy("order");
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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
       
        $show = new Show(CommonCode::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('group', __('Nhóm'));
        $show->field('type', __('Thể loại'));
        $show->field('value', __('Giá trị'));
        $show->field('description_vi', __('Mô tả tiếng việt'));
        $show->field('order', __('Sắp xếp'));
        $show->field('status', __('Trạng thái'))->as($statusFormatter);
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->model()->where('business_id', '=', Admin::user()->business_id);
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
        $form = new Form(new CommonCode());
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();
        if ($form->isEditing()) {
            $form->tools(function (Form\Tools $tools) {
                $tools->disableDelete();
            });
            $form->text('group', __('Nhóm'))->disable();
            $form->text('type', __('Thể loại'))->disable();
            $form->text('value', __('Giá trị'))->disable();
        }else{
            $form->text('group', __('Nhóm'));
            $form->text('type', __('Thể loại'));
            $form->text('value', __('Giá trị'));
        }
        $form->hidden('business_id')->value($business->id);
        $form->text('description_vi', __('Mô tả tiếng việt'));
        $form->text('description_en', __('Mô tả tiếng anh'));
        $form->text('order', __('Sắp xếp'));
        $form->select('block_delete', __('Chặn xoá'))->options(array(0 => 'Không', 1 => 'Có'))->required();
        $form->select('status', __('Trạng thái'))->options(array(1 => 'Hoạt động', 2 => 'Không hoạt động'))->required();
      
        // $url = 'http://127.0.0.1:8000/api/business';
        // $url = env('APP_URL') . '/api/business';
        $url = 'https://business.metaverse-solution.vn/api/business';
        
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