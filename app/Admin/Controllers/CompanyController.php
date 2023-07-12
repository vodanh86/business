<?php

namespace App\Admin\Controllers;

use App\Http\Models\Business;
use App\Http\Models\Company;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Show;


class CompanyController extends AdminController{
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
            return $value == 1 ? 'ACTIVE' : 'UNACTIVE';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new Company());
        
        $grid->column('id', __('Id'));
        $grid->column('business.code', __('Mã doanh nghiệp'));
        $grid->column('code', __('Mã công ty'));
        $grid->column('name', __('Tên'));
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
        $show = new Show(Company::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('business.code', __('Mã doanh nghiệp'));
        $show->field('code', __('Mã công ty'));
        $show->field('name', __('Tên'));
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
        $businesses = Business::with('companies')->get()->pluck('code', 'id');
        $form = new Form(new Company());
        $form->divider('1. Doanh nghiệp');
        $form->select('business_code', __('Mã doanh nghiệp'))->options($businesses)->required();
        $form->text('type_business', __('Loại doanh nghiệp'))->disable();
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();

        $form->divider('2. Công ty');
        $form->text('code', __('Mã công ty'))->required();
        $form->text('name', __('Tên công ty'))->required();
        $form->select('status', __('Trạng thái'))->options(array(1 => 'ACTIVE', 2 => 'UNACTIVE'))->required();

        // $url = 'http://127.0.0.1:8000/api/business';
        $url = env('APP_URL') . '/api/contract';
        
        $script = <<<EOT
        $(document).on('change', ".business_code", function () {
            $.get("$url",{q : this.value}, function (data) {
                $("#type_business").val(data.type);
                $("#name_business").val(data.name);  
            });
        });
        EOT;

        Admin::script($script);
        return $form;
    }
}