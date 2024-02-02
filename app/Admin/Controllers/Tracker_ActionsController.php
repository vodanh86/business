<?php

namespace App\Admin\Controllers;

use App\Http\Models\Tracker\TrackerActions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;

class Tracker_ActionsController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Hành động';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TrackerActions());
        $grid->column('action_id', __('ID hành động'))->filter();
        $grid->column('appCreator', __('appCreator'))->filter();
        $grid->column('limits', __('limits'))->filter();
        $grid->column('data', __('Dữ liệu'))->filter();
        $grid->column('type', __('Loại'))->filter();
        $grid->column('date', __('Ngày tạo'))->filter();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('action_id', __('ID hành động'));
            $filter->equal('trello_id', __('ID Trello'));
            $filter->equal('idMemberCreator', __('ID người tạo'));
            $filter->equal('memberCreator', __('Người tạo'));
            $filter->equal('member', __('Người tham gia'));
            $filter->equal('data', __('Dữ liệu'));
            $filter->equal('type', __('Loại'));
            $filter->date('date', __('Ngày tạo'));
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
        $show = new Show(TrackerActions::findOrFail($id));
        $show->field('action_id', __('ID hành động'));
        $show->field('trello_id', __('ID Trello'));
        $show->field('idMemberCreator', __('ID người tạo'));
        $show->field('memberCreator', __('Người tạo'));
        $show->field('member', __('Người tham gia'));

        $show->field('data', __('Dữ liệu'));
        $show->field('type', __('Loại'));
        $show->field('date', __('Ngày tạo'))->as(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return;
    }
}