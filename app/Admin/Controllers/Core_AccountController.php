<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
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
       
        $grid = new Grid(new Account());
        $grid->column('number', __('Số tài khoản'))->copyable();
        $grid->column('type', __('Loại'));
        $grid->column('bank_name', __('Tên ngân hàng'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "grid");
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return ConstantHelper::dateFormatter($updatedAt);
        });
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
        $show = new Show(Account::findOrFail($id));

        $show->field('number', __('Số tài khoản'));
        $show->field('type', __('Loại'));
        $show->field('bank_name', __('Tên ngân hàng'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", "detail");
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
        $business = (new UtilsCommonHelper)->currentBusiness();
        $statusOptions = (new UtilsCommonHelper)->commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();

        $form = new Form(new Account());
        $form->hidden('business_id')->value($business->id);
        $form->text('number', __('Số tài khoản'))->required();
        $form->select('type', __('Loại'))->options(array("Asset" => "Asset", "Liabilities" => "Liabilities"));
        $form->select('bank_name', __('Tên ngân hàng'))->required();
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();

        $url = "https://api.vietqr.io/v2/banks";
        $script = <<<EOT
        $(function() {
            var bankSelect = $(".bank_name");
            var optionsBank = {};

            $.get("$url", function (banks) {
                var currentData = banks.data
                $.each(currentData, function (index, bank) {
                    optionsBank[bank.id] = bank.shortName;
                });
                bankSelect.empty();
                bankSelect.append($('<option>', {
                    value: '',
                    text: ''
                }));
                $.each(optionsBank, function (id, bankShortName) {
                    bankSelect.append($('<option>', {
                        value: id,
                        text: bankShortName
                    }));
                });
                bankSelect.trigger('change');
            });
        });
        EOT;
        Admin::script($script);
        
        return $form;
    }
}