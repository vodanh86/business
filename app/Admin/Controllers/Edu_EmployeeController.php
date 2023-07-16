<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Edu\EduEmployee;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_EmployeeController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Nhân viên';

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
        $grid = new Grid(new EduEmployee());
        
        $grid->column('business.name', __('Tên doanh nghiệp'));
        $grid->column('name', __('Họ và tên'));
        $grid->column('phone', __('Số điện thoại'));
        $grid->column('status', __('Trạng thái'))->display($statusFormatter);
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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'ACTIVE' : 'UNACTIVE';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $show = new Show(EduEmployee::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('name', __('Họ và tên'));
        $show->field('phone', __('Số điện thoại'));
        $show->field('status', __('Trạng thái'))->as($statusFormatter);
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
        $form = new Form(new EduEmployee());
        $form->hidden('business_id')->value($business->id);
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();
        $form->text('name', __('Họ và tên'));
        $form->mobile('phone', __('Số điện thoại'));
        $form->select('status', __('Trạng thái'))->options(array(1 => 'ACTIVE', 2 => 'UNACTIVE'))->required();

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