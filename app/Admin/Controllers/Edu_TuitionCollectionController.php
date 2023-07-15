<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Business;
use App\Http\Models\Edu\EduTuitionCollection;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_TuitionCollectionController extends AdminController{
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
        $grid = new Grid(new EduTuitionCollection());
        
        $grid->column('business.name', __('Tên doanh nghiệp'))->width(150);
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
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $statusFormatter = function ($value) {
            return $value == 1 ? 'ACTIVE' : 'UNACTIVE';
        };
        $moneyFormatter = function ($money) {
            return number_format($money, 2, ',', ' ') . " VND";
        };
        $dateFormatter = function ($updatedAt) {
            $carbonUpdatedAt = Carbon::parse($updatedAt);
            return $carbonUpdatedAt->format('d/m/Y - H:i:s');
        };
        $show = new Show(EduTuitionCollection::findOrFail($id));

        $show->field('business.name', __('Tên doanh nghiệp'));
        $show->field('student', __('Học sinh'))->width(150);
        $show->field('processing_date', __('Ngày nghiệm thu'))->width(150);
        $show->field('value_date', __('Ngày nộp tiền'))->as($dateFormatter)->width(150);
        $show->field('amount', __('Số lượng'))->width(150);
        $show->field('unit_price', __('Đơn giá'))->as($moneyFormatter)->width(150);
        $show->field('value', __('Giá trị'))->as($moneyFormatter)->width(150);
        $show->field('next_date', __('Ngày tiếp theo'))->as($dateFormatter)->width(150);
        $show->field('description', __('Mô tả'))->width(150);
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
        $business = Business::where('id', Admin::user()->business_id)->first();
        $form = new Form(new EduTuitionCollection());

        $form->divider('1. Doanh nghiệp');
        $form->hidden('business_id')->value($business->id);

        $form->text('type_business', __('Loại doanh nghiệp'))->disable();
        $form->text('name_business', __('Tên doanh nghiệp'))->disable();

        $form->divider('2. Thu học phí');
        $form->select('class', __('Lớp học'))->required();
        $form->select('student', __('Học sinh'))->required();
        $form->date('processing_date', __('Ngày nghiệm thu'))->required();
        $form->date('value_date', __('Ngày nộp tiền'))->required();
        $form->text('amount', __('Số lượng'))->required();
        $form->currency('unit_price', __('Đơn giá'))->symbol('VND')->required();
        $form->currency('value', __('Giá trị'))->symbol('VND')->required();
        $form->date('next_date', __('Ngày tiếp theo'))->required();
        $form->text('description', __('Mô tả'));

        $url = 'https://business.metaverse-solution.vn/api/business';
        // $url = env('APP_URL') . '/api/business';
        
        $script = <<<EOT

        $(function() {
            var businessId = $(".business_id").val();
            
            $.get("$url",{q : businessId}, function (data) {
                $("#type_business").val(data.type);
                $("#name_business").val(data.name);  
            });
        });
        EOT;

        Admin::script($script);
        return $form;
    }
}