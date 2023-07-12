<?php

namespace App\Admin\Controllers;

use App\Http\Models\BusinessClass;
use App\Http\Models\Student;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;


class StudentController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Học sinh';

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
        $grid = new Grid(new Student());
        
        $grid->column('id', __('Id'));
        $grid->column('name', __('Tên'));
        $grid->column('email', __('Email'));
        $grid->column('phone_number', __('Số điện thoại'));
        $grid->column('address', __('Địa chỉ'));
        $grid->column('last_call', __('Liên lạc gần nhất'));
        $grid->column('school', __('Trường'));
        $grid->column('wom', __('WOM'));
        $grid->column('channel', __('Kênh'));
        $grid->column('status', __('Trạng thái'));
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
        $businessClasses = BusinessClass::with('classes')->get()->pluck('code', 'id');
        $form = new Form(new Student());
        // $grid->column('class', __('classId'));
        $form->divider('1. Thông tin lớp học');
        $form->select('class_code', __('Mã lớp học'))->options($businessClasses)->required();
        $form->text('name', __('Tên lớp học'))->disable();
        $form->text('schedule', __('Lịch học'))->disable();
        $form->text('teacher', __('Giảng viên'))->disable();


        $form->divider('2. Thông tin học sinh');
        $form->text('name', __('Tên'));
        $form->text('email', __('Email'));
        $form->text('phone_number', __('Số điện thoại'));
        $form->text('address', __('Địa chỉ'));
        $form->text('last_call', __('Liên lạc gần nhất'));
        $form->text('school', __('Trường'));
        $form->text('wom', __('WOM'));
        $form->text('channel', __('Kênh'));
        $form->select('status', __('Trạng thái'))->options(array(1 => 'ACTIVE', 2 => 'UNACTIVE'))->required();
      
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