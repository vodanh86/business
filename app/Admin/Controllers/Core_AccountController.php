<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Business;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Show;

class Core_AccountController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tài khoản';

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
        $grid = new Grid(new Account());
        
        $grid->column('number', __('Số tài khoản'));
        $grid->column('type', __('Loại'));
        $grid->column('bank_name', __('Tên ngân hàng'));
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
        $show = new Show(Account::findOrFail($id));

        $show->field('number', __('Số tài khoản'));
        $show->field('type', __('Loại'));
        $show->field('bank_name', __('Tên ngân hàng'));
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

        $form = new Form(new Account());
        $form->divider('1. Doanh nghiệp');
        $form->hidden('business_id')->value($business->id);
        
        $form->text('type_business', __('Loại doanh nghiệp'))->disable();
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();

        $form->divider('2. Thông tin tài khoản');
        $form->text('number', __('Số tài khoản'));
        $form->select('type', __('Loại'))->options(array("Asset" => "Asset", "Liabilities"));
        $form->text('bank_name', __('Tên ngân hàng'));
        $form->select('status', __('Trạng thái'))->options(array(1 => 'Hoạt động', 2 => 'Không hoạt động'))->required();

        // $url = 'http://127.0.0.1:8000/api/business';
        // $url = env('APP_URL') . '/api/business';
        $url = 'https://business.metaverse-solution.vn/api/business';

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