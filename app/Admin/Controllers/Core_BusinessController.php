<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Carbon\Carbon;


class Core_BusinessController extends AdminController{
 /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Doanh nghiệp';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $grid = new Grid(new Business());
        
        $grid->column('id', __('ID'));
        $grid->column('code', __('Mã'));
        $grid->column('type', __('Loại'));
        $grid->column('name', __('Tên'));
        $grid->column('status', __('Trạng thái'))->display($statusFormatter);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
        $grid->model()->orderBy('id', 'desc');
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
        $statusFormatter = function ($value) {
            return $value == 1 ? 'Hoạt động' : 'Không hoạt động';
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $show = new Show(Business::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('code', __('Mã'));
        $show->field('type', __('Loại'));
        $show->field('name', __('Tên'));
        $show->field('status', __('Trạng thái'))->display($statusFormatter);
        $show->field('created_at', __('Ngày tạo'))->display($dateFormatter);
        $show->field('updated_at', __('Ngày cập nhật'))->display($dateFormatter);

        return $show;
    }
     /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Business());
        $form->text('code', __('Mã'))->required();
        $form->text('type', __('Loại'))->required();
        $form->text('name', __('Tên'))->required();
        $form->select('status', __('Trạng thái'))->options(array(1 => 'ACTIVE', 2 => 'UNACTIVE'))->required();
        return $form;
    }
}