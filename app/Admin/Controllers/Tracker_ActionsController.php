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
        $grid->column('action_id', __('ID hành động'));
        $grid->column('trello_id', __('ID Trello'));
        $grid->column('idMemberCreator', __('ID người tạo'));
        $grid->column('memberCreator', __('Người tạo'));
        $grid->column('member', __('Người tham gia'));

        $grid->column('data', __('Dữ liệu'));
        $grid->column('type', __('Loại'));
        $grid->column('date', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
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