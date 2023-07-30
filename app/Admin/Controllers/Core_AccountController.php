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
        $grid->column('bank_name', __('Tên ngân hàng'));
        $grid->column('number', __('Số tài khoản'))->copyable();
        $grid->column('balance', __('Số dư'))->display(function ($money) {
            return ConstantHelper::moneyFormatter($money);
        });
        $grid->column('type', __('Loại'));
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

        $show->field('bank_name', __('Tên ngân hàng'));
        $show->field('number', __('Số tài khoản'));
        $show->field('type', __('Loại'));
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
        if ($form->isEditing()) {
            $id = request()->route()->parameter('bankaccount');
            $bankName = $form->model()->find($id)->getOriginal("bank_name");
            $form->select('bank_name', __('Tên ngân hàng'))->options(function () use ($bankName) {
                $options = ['' => ''];
                $url = "https://api.vietqr.io/v2/banks";
                $response = file_get_contents($url);
                $data = json_decode($response);
                if ($data && isset($data->data) && is_array($data->data)) {
                    foreach ($data->data as $bank) {
                        $options[$bank->shortName] = $bank->shortName;
                    }
                }
                if ($bankName && !in_array($bankName, array_keys($options))) {
                    $options[$bankName] = $bankName;
                }
                return $options;
            })->default($bankName)->required();
        }else{
            $form->select('bank_name', __('Tên ngân hàng'))->required();
        }
        $form->text('number', __('Số tài khoản'))->required();
        $form->select('type', __('Loại'))->options(array("Asset" => "Asset", "Liabilities" => "Liabilities"));
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();

        $url = "https://api.vietqr.io/v2/banks";
        $urlCheckAccountNumber = "https://api.vietqr.io/v2/lookup";

        $script = <<<EOT
        $(function() {
            var bankSelect = $(".bank_name");
            var accountNumber = $(".number");
            var accountName = $(".account_name");
            var optionsBank = {};

            $.get("$url", function (banks) {
                var currentData = banks.data
                $.each(currentData, function (index, bank) {
                    optionsBank[bank.bin] = bank.shortName;
                });
                $.each(optionsBank, function (bin, bankShortName) {
                    bankSelect.append($('<option>', {
                        value: bankShortName,
                        text: bankShortName
                    }));
                });
                bankSelect.trigger('change');
            });

            // Kiem tra ten tai khoan
            // bankSelect.on('change', function() {
            //     var binVal = $(this).val();
            // });
            
            // accountNumber.on('change', function() {
            //     var binVal = bankSelect.val();
            //     var accountNumberVal = $(this).val();
            //     var data = JSON.stringify({
            //         "bin": binVal,
            //         "accountNumber": accountNumberVal,
            //     });
            
            //     if(!binVal && !accountNumberVal) return
            //     $.ajax({
            //         url: "$urlCheckAccountNumber",
            //         type: 'POST',
            //         data: data,
            //         headers: {
            //           'x-client-id': '95b09fba-29b9-4875-9b5c-49b4fed6e691',
            //           'x-api-key': 'cd4e4b44-fb97-49ce-beb2-c99ef29ada37',
            //           'Content-Type': 'application/json'
            //         },
            //         success: function (response) {
            //             accountName = response && response.data.accountName
            //         },
            //         error: function (error) {
            //           console.log(error);
            //         }
            //       });
            // });
        });
        EOT;
        Admin::script($script);
        
        return $form;
    }
}