<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Business;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

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
        
        $grid->column('id', __('ID'));
        $grid->column('number', __('Số tài khoản'));
        $grid->column('type', __('Loại'));
        $grid->column('bank_name', __('Tên ngân hàng'));
        $grid->column('status', __('Trạng thái'))->display($statusFormatter);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
        return $grid;
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
        $form->display('business_code', __('Mã doanh nghiệp'))->default($business->code);
        $form->hidden('business_id')->value($business->id);

        $form->divider('1. Thông tin tài khoản');
        $form->text('number', __('Số tài khoản'));
        $form->select('type', __('Loại'))->options(array("Asset" => "Asset", "Liabilities"));
        $form->text('bank_name', __('Tên ngân hàng'));
        $form->select('status', __('Trạng thái'))->options(array(1 => 'Hoạt động', 2 => 'Không hoạt động'))->required();

        // $url = 'http://127.0.0.1:8000/api/contract';
        // $url = env('APP_URL') . '/api/contract';
        
        // $script = <<<EOT
        // $(document).on('change', ".contract_id", function () {
        //     $.get("$url",{q : this.value}, function (data) {
        //         $("#property").val(data.property);
        //         $(".customer_type").val(parseInt(data.customer_type)).change();
        //         $("#tax_number").val(data.tax_number);  
        //         $("#business_name").val(data.business_name);
        //         $("#personal_address").val(data.personal_address);
        //         $("#business_address").val(data.business_address);
        //         $("#representative").val(data.representative);
        //         $("#position").val(data.position);
        //         $("#personal_name").val(data.personal_name);
        //         $("#id_number").val(data.id_number);  
        //         $("#issue_place").val(data.issue_place);  
        //         $("#issue_date").val(data.issue_date); 
        //     });
        // });
        // EOT;

        // Admin::script($script);
        return $form;
    }
}