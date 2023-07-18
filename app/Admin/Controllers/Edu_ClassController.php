<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduClass;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
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
        $status = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Status')
            ->where('value', $value)
            ->first();
            if ($commonCode) {
                return $value === 1 ? "<span class='label label-success'>$commonCode->description_vi</span>" : "<span class='label label-danger'>$commonCode->description_vi</span>";
            }
            return '';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new EduClass());
        
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('name', __('Tên lớp'));
        $grid->column('description', __('Mô tả'));
        $grid->column('status', __('Trạng thái'))->display($status);
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
        $show = new Show(EduClass::findOrFail($id));

        $show->field('branch.code', __('Mã chi nhánh'));
        $show->field('name', __('Tên lớp'));
        $show->field('description', __('Mô tả'));
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
       
        $form = new Form(new EduClass());
        $form->hidden('business_id')->value($business->id);
        $form->select('branch_id', __('Tên chi nhánh'))->options()->required();
        $form->text('name', __('Tên lớp'));
        $form->text('description', __('Mô tả'));
        $form->select('status', __('Trạng thái'))->options($status)->required();

        // $urlBranch = env('APP_URL') . '/api/branch';
        // $urlBusiness = env('APP_URL') . '/api/business';
        $urlBranch = 'https://business.metaverse-solution.vn/api/branch';
        $urlBusiness = 'https://business.metaverse-solution.vn/api/business';

        
        $script = <<<EOT
        $(function() {
            var businessId = $(".business_id").val();
            var branchSelect = $(".branch_id");
            var options = {};
            
            $.get("$urlBusiness",{q : businessId}, function (data) {
                $("#type_business").val(data.type);
                $("#name_business").val(data.name);  
            });
            $.get("$urlBranch", { business_id: businessId }, function (branches) {
                $.each(branches, function (index, branch) {
                    options[branch.id] = branch.branch_name;
                });
                branchSelect.empty();
                branchSelect.append($('<option>', {
                    value: '',
                    text: ''
                }));
                $.each(options, function (id, branchName) {
                    branchSelect.append($('<option>', {
                        value: id,
                        text: branchName
                    }));
                });
                branchSelect.trigger('change');
            }).done(function() {
                branchSelect.val(Object.keys(options)[0]).trigger('change');
            });
        });
        
        EOT;
        Admin::script($script);
        return $form;
    }
}