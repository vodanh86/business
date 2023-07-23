<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\Entries;
use App\Http\Models\Edu\EduTeacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Core_EntriesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Lịch sử giao dịch';

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

        $grid = new Grid(new Entries());

        $grid->column('branch.branch_name', __('Tên chi nhánh'));
        $grid->column('transaction.name', __('Mã giao dịch'));
        $grid->column('account', __('Số tài khoản'));
        $grid->column('account_name', __('Tên tài khoản'));
        $grid->column('amount', __('Số tiền'));
        $grid->column('value_date', __('Ngày thực hiện'));
        $grid->column('trans_reference', __('Tham chiếu giao dịch'));
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->model()->where('business_id', '=', Admin::user()->business_id);
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->filter(function ($filter) {
            $filter->group('branch.branch_name', __('Tên chi nhánh'), function ($group) {
                $group->gt('Lớn hơn');
                $group->lt('Nhỏ hơn');
                $group->nlt('Không nhỏ hơn');
                $group->ngt('Không lớn hơn');
                $group->equal('Bằng với');
            });

            $filter->group('transaction.name', __('Mã giao dịch'), function ($group) {
                $group->gt('Lớn hơn');
                $group->lt('Nhỏ hơn');
                $group->nlt('Không nhỏ hơn');
                $group->ngt('Không lớn hơn');
                $group->equal('Bằng với');
            });

            $filter->group('account', __('Tài khoản'), function ($group) {
                $group->gt('Lớn hơn');
                $group->lt('Nhỏ hơn');
                $group->nlt('Không nhỏ hơn');
                $group->ngt('Không lớn hơn');
                $group->equal('Bằng với');
            });

            $filter->group('amount', __('Số tiền'), function ($group) {
                $group->gt('Lớn hơn');
                $group->lt('Nhỏ hơn');
                $group->nlt('Không nhỏ hơn');
                $group->ngt('Không lớn hơn');
                $group->equal('Bằng với');
            });
            $filter->group('value_date', __('Ngày thực hiện'), function ($group) {
                $group->gt('Lớn hơn');
                $group->lt('Nhỏ hơn');
                $group->nlt('Không nhỏ hơn');
                $group->ngt('Không lớn hơn');
                $group->equal('Bằng với');
            });
            $filter->group('trans_reference', __('Tham chiếu giao dịch'), function ($group) {
                $group->gt('Lớn hơn');
                $group->lt('Nhỏ hơn');
                $group->nlt('Không nhỏ hơn');
                $group->ngt('Không lớn hơn');
                $group->equal('Bằng với');
            });
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
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $show = new Show(Entries::findOrFail($id));

        $show->field('branch.branch_name', __('Tên chi nhánh'));
        $show->field('transaction.name', __('Mã giao dịch'));
        $show->field('account', __('Tài khoản'));
        $show->field('amount', __('Số tiền'));
        $show->field('value_date', __('Ngày thực hiện'));
        $show->field('trans_reference', __('Tham chiếu giao dịch'));
        $show->field('description', __('Mô tả'));
        // $show->field('description', __('Mô tả'))->title()->as(function ($title) {
        //     return "<textarea>$title</textarea>";
        // });
        $show->field('created_at', __('Ngày tạo'))->display($dateFormatter);
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });;
        return $show;
    }
}
