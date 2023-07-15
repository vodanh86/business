<?php

namespace App\Admin\Controllers;

use App\Admin\Controllers\Constant;
use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'ACTIVE' : 'UNACTIVE';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new EduClass());
        
        $grid->column('id', __('ID'));
        $grid->column('business.name', __('Tên doanh nghiệp'));
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('name', __('Tên lớp'));
        $grid->column('description', __('Mô tả'));
        $grid->column('schedule', __('Lịch học'));
        $grid->column('teacher', __('Giảng viên'));
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
            return $value == 1 ? 'ACTIVE' : 'UNACTIVE';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $show = new Show(EduClass::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('business.code', __('Mã doanh nghiệp'));
        $show->field('branch.code', __('Mã chi nhánh'));
        $show->field('name', __('Tên lớp'));
        $show->field('description', __('Mô tả'));
        $show->field('schedule', __('Lịch học'));
        $show->field('teacher', __('Giảng viên'));
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

        $form = new Form(new EduClass());
        $form->divider('1. Doanh nghiệp&chi nhánh');
        $form->hidden('business_id')->value($business->id);
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();
        $form->select('branch_id', __('Mã chi nhánh'))->options()->required();

        $form->divider('2. Thông tin lớp học');
        $form->text('name', __('Tên lớp'));
        $form->text('description', __('Mô tả'));
        $form->multipleSelect('schedule', __('Lịch học'))->options(Constant::SCHEDULE_CLASS)->setWidth(5, 2)->required();
        $form->select('teacher', __('Giảng viên'));
        $form->select('status', __('Trạng thái'))->options(array(1 => 'ACTIVE', 2 => 'UNACTIVE'))->required();

        // $urlBranch = 'http://127.0.0.1:8000/api/branch';
        // $urlBusiness = 'http://127.0.0.1:8000/api/business';
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