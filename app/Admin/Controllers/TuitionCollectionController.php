<?php

namespace App\Admin\Controllers;

use App\Http\Models\Business;
use App\Http\Models\TuitionCollection;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;


class TuitionCollectionController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Thu học phí';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $moneyFormatter = function ($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };
        $grid = new Grid(new TuitionCollection());
        
        $grid->column('id', __('Id'));
        $grid->column('business.code', __('Mã doanh nghiệp'));
        $grid->column('company.code', __('Mã công ty'));
        $grid->column('student', __('Học sinh'))->width(150);
        $grid->column('processing_date', __('Ngày nghiệm thu'))->width(150);
        $grid->column('value_date', __('Ngày nộp tiền'))->display($dateFormatter)->width(150);
        $grid->column('amount', __('Số lượng'))->width(150);
        $grid->column('unit_price', __('Đơn giá'))->display($moneyFormatter)->width(150);
        $grid->column('value', __('Giá trị'))->display($moneyFormatter)->width(150);
        $grid->column('next_date', __('Ngày tiếp theo'))->display($dateFormatter)->width(150);
        $grid->column('description', __('Mô tả'))->width(150);
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
        $businesses = Business::with('companies')->get()->pluck('code', 'id');
        $form = new Form(new TuitionCollection());
        $form->select('business_code', __('Mã doanh nghiệp'))->options($businesses)->required();
        $form->text('student', __('Học sinh'))->required();
        $form->date('processing_date', __('Ngày nghiệm thu'))->required();
        $form->date('value_date', __('Ngày nộp tiền'))->required();
        $form->text('amount', __('Số lượng'))->required();
        $form->number('unit_price', __('Đơn giá'))->required();
        $form->number('value', __('Giá trị'))->required();
        $form->date('next_date', __('Ngày tiếp theo'))->required();
        $form->text('description', __('Mô tả'));

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