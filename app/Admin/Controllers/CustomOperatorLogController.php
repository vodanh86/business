<?php

namespace App\Admin\Controllers;

use App\Http\Models\AdminUser;
use Encore\Admin\Grid;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Support\Arr;
use Encore\Admin\Controllers\LogController;
use Encore\Admin\Facades\Admin;

class CustomOperatorLogController extends LogController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.operation_log');
    }

    /**
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OperationLog());
        $userIds = AdminUser::where('business_id', Admin::user()->business_id)->pluck('id')->toArray();

        $grid->model()->orderBy('id', 'DESC');

        $grid->column('user.name', 'Tên');
        $grid->column('method', 'Phương thức')->display(function ($method) {
            $color = Arr::get(OperationLog::$methodColors, $method, 'grey');

            return "<span class=\"badge bg-$color\">$method</span>";
        });
        $grid->column('path', 'Đường dẫn')->label('info');
        $grid->column('ip', 'Địa chỉ IP')->label('primary');
        $grid->column('input', 'Giá trị nhập')->display(function ($input) {
            $input = json_decode($input, true);
            $input = Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);
            if (empty($input)) {
                return '<code>{}</code>';
            }

            return '<pre>'.json_encode($input, JSON_PRETTY_PRINT | JSON_HEX_TAG).'</pre>';
        });

        $grid->column('created_at', trans('admin.created_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->disableCreateButton();
        
        $grid->model()->whereIn('user_id', $userIds);
        $grid->fixColumns(0, 0);

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $userModel = AdminUser::where('business_id', Admin::user()->business_id);

            $filter->equal('user_id', 'Tên')->select($userModel->pluck('name', 'id'));
            $filter->date('created_at', 'Ngày tạo');
            $filter->date('updated_at', 'Ngày cập nhật');
            $filter->equal('Phương thức')->select(array_combine(OperationLog::$methods, OperationLog::$methods));
            $filter->like('Đường dẫn');
            $filter->equal('Địa chỉ IP');
        });
        $grid->expandFilter();

        return $grid;
    }
}