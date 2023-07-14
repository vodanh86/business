<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\Branch;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Show;


class Core_BranchController extends AdminController{
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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };

        $grid = new Grid(new Branch());
        
        $grid->column('id', __('ID'));
        $grid->column('business.code', __('Mã doanh nghiệp'));
        $grid->column('business.type', __('Loại doanh nghiệp'));
        $grid->column('branch_code', __('Mã chi nhánh'));
        $grid->column('branch_name', __('Tên chi nhánh'));
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
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $show = new Show(Branch::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('business.code', __('Mã doanh nghiệp'));
        $show->field('business.type', __('Loại doanh nghiệp'));
        $show->field('branch_code', __('Mã chi nhánh'));
        $show->field('branch_name', __('Tên chi nhánh'));
        $show->field('status', __('Trạng thái'))->display($statusFormatter);
        $show->field('created_at', __('Ngày tạo'))->display($dateFormatter);
        $show->field('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
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

        $form = new Form(new Branch());
        $form->divider('1. Doanh nghiệp');
        $form->display('business_code', __('Mã doanh nghiệp'))->default($business->code);
        $form->hidden('business_id')->value($business->id);

        $form->text('type_business', __('Loại doanh nghiệp'))->disable();
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();

        $form->divider('2. Chi nhánh');
        $form->text('branch_code', __('Mã chi nhánh'))->required();
        $form->text('branch_name', __('Tên chi nhánh'))->required();
        $form->select('status', __('Trạng thái'))->options(array(1 => 'Hoạt động', 2 => 'Không hoạt động'))->required();

        $url = 'http://127.0.0.1:8000/api/business';
        // $url = env('APP_URL') . '/api/business';
        
        $script = <<<EOT

        $(function() {
            var businessId = $(".business_id").val();
            
            $.get("$url",{q : businessId}, function (data) {
                $("#type_business").val(data.type);
                $("#name_business").val(data.name);  
            });
        });
        EOT;

        Admin::script($script);
        return $form;
    }
}