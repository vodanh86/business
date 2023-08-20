<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduTuitionCollection;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Show;
use Encore\Admin\Grid;

class Edu_HistoryTuitionCollectionController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Lịch sử thu học phí';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduTuitionCollection());
        $grid->column('trans_ref', __('Mã giao dịch'));
        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('schedule.name', __('Lịch học'));
        $grid->column('student.name', __('Học sinh'));
        $grid->column('processing_date', __('Ngày nộp tiền'))->display(function ($processingDate) {
            return ConstantHelper::dayFormatter($processingDate);
        });
        $grid->column('value_date', __('Ngày bắt đầu học'))->display(function ($valueDate) {
            return ConstantHelper::dayHightLightFormatter($valueDate, "valueDate");
        });
        $grid->column('next_date', __('Ngày tiếp theo'))->display(function ($nextDate) {
            return ConstantHelper::dayHightLightFormatter($nextDate, "nextDate");
        });
        $grid->column('amount', __('Số lượng'));
        $grid->column('unit_price', __('Đơn giá'))->display(function ($unitPrice) {
            return ConstantHelper::moneyFormatter($unitPrice);
        });
        $grid->column('value', __('Giá trị'))->display(function ($value) {
            return ConstantHelper::moneyFormatter($value);
        });
        $grid->column('account_id', __('Số tài khoản'))->display(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountGridFormatter($accountNumber);
        });
        $grid->column('status', __('Trạng thái'))->display(function ($value) {
            return ConstantHelper::transactionGridRecordStatus($value);
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return ConstantHelper::dateFormatter($updatedAt);
        });
        $grid->model()
            ->select('id','trans_ref', 'business_id', 'branch_id', 'schedule_id', 'student_id', 'processing_date', 'value_date', 'next_date', 'amount', 'unit_price', 'value', 'account_id', 'status', 'created_at', 'updated_at')
            ->whereNotIn('created_at', function ($query) {
                $query->selectRaw('MAX(created_at)')
                    ->from('edu_tuition_collection')
                    ->groupBy('student_id');
        });
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->model()->orderBy("next_date");
        $grid->fixColumns(0, 0);
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('trans_ref', 'Mã giao dịch');
        });
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
        $show = new Show(EduTuitionCollection::findOrFail($id));

        $show->field('trans_ref', __('Mã giao dịch'));
        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('schedule.name', __('Lịch học'));
        $show->field('student.name', __('Học sinh'));
        $show->field('processing_date', __('Ngày nộp tiền'))->as(function ($processingDate) {
            return ConstantHelper::dayFormatter($processingDate);
        });
        $show->field('value_date', __('Ngày bắt đầu học'))->as(function ($valueDate) {
            return ConstantHelper::dayFormatter($valueDate);
        });
        $show->field('next_date', __('Ngày tiếp theo'))->as(function ($nextDate) {
            return ConstantHelper::dayFormatter($nextDate);
        });
        $show->field('amount', __('Số lượng'));
        $show->field('unit_price', __('Đơn giá'))->as(function ($unitPrice) {
            return ConstantHelper::moneyFormatter($unitPrice);
        });
        $show->field('value', __('Giá trị'))->as(function ($value) {
            return ConstantHelper::moneyFormatter($value);
        });
        $show->field('account_id', __('Tài khoản'))->as(function ($accountNumber) {
            return UtilsCommonHelper::bankAccountDetailFormatter($accountNumber);
        });
        $show->field('description', __('Mô tả'));
        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return ConstantHelper::transactionDetailRecordStatus($status);
        });
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
                $tools->disableEdit();
            });;
        return $show;
    }
}
