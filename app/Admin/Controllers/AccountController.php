<?php

namespace App\Admin\Controllers;

use App\Http\Models\BusinessAccount;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Carbon\Carbon;


class AccountController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Branch';

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
        $grid = new Grid(new BusinessAccount());
        
        $grid->column('id', __('Id'));
        $grid->column('number', __('number'))->display($moneyFormatter);
        $grid->column('type', __('type'));
        $grid->column('bank_name', __('bank_name'));
        $grid->column('status', __('status'));
        $grid->column('created_at', __('created_at'))->display($dateFormatter);
        $grid->column('updated_at', __('updated_at'))->display($dateFormatter);
        return $grid;
    }
     /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BusinessAccount());
        $status = array();
        // if ($form->isEditing()) {
        //     $id = request()->route()->parameter('contract_acceptance');
        //     $model = $form->model()->find($id);
        //     $currentStatus = $model->status;
        //     $nextStatuses = StatusTransition::where(["table" => Constant::CONTRACT_ACCEPTANCE_TABLE, "status_id" => $currentStatus])->where('editors', 'LIKE', '%'.Admin::user()->roles[0]->slug.'%')->get();
        //     $status[$model->status] = $model->statusDetail->name;
        //     foreach($nextStatuses as $nextStatus){
        //         $status[$nextStatus->next_status_id] = $nextStatus->nextStatus->name;
        //     }
        // } else {
        //     $nextStatuses = StatusTransition::where("table", Constant::CONTRACT_ACCEPTANCE_TABLE)->whereNull("status_id")->get();
        //     foreach ($nextStatuses as $nextStatus) {
        //         $status[$nextStatus->next_status_id] = $nextStatus->nextStatus->name;
        //     }
        // }
        // $form->select('class_id', __('valuation_document.contract_id'))->options(Contract::where("branch_id", Admin::user()->branch_id)->where('status', Constant::CONTRACT_INPUTTING_STATUS)->pluck('code', 'id'));
        $form->number('number', __('number'));
        $form->select('type', __('type'))->options(array("Asset" => "Asset", "Liabilities"));
        $form->text('bank_name', __('bank_name'));
        $form->select('status', __('status'))->options(array(1 => 1, 2));

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