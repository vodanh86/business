<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduClass;
use App\Http\Models\Edu\EduStudent;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;

class Edu_StudentController extends AdminController{
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
        $wom = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'WOM')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };
        $channel = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Channel')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };
        $grade = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Grade')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };
        $location = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Location')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };
        $school = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'School')
            ->where('value', $value)
            ->first();
            return $commonCode ? $commonCode->description_vi : '';
        };
        $status = function ($value) {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('type', 'Status')
            ->where('value', $value)
            ->first();
            if ($commonCode) {
                return $value === 1 ? "<span class='label label-success'>$commonCode->description_vi</span>" : "<span class='label label-danger'>$commonCode->description_vi</span>";
            }
            return '';
        };
        
        $grid = new Grid(new EduStudent());

        $grid->column('branch.branch_name', __('Tên chi nhánh'))->width(150);
        $grid->column('class.name', __('Tên lớp'))->width(150);
        $grid->column('channel', __('Kênh'))->display($channel);
        $grid->column('wom', __('WOM'))->display($wom);
        $grid->column('source', __('Nguồn'));
        $grid->column('name', __('Tên học sinh'));
        $grid->column('parent', __('Bố mẹ'));
        $grid->column('phone_number', __('Số điện thoại'));
        $grid->column('last_call', __('Liên lạc gần nhất'));
        $grid->column('contact_status', __('Trạng thái liên lạc'));
        $grid->column('grade', __('Khối'))->display($grade);
        $grid->column('location', __('Địa chỉ'))->display($location);
        $grid->column('school', __('Trường'))->display($school);
        $grid->column('status', __('Trạng thái'))->display($status);
        $grid->column('created_at', __('Ngày tạo'))->display($dateFormatter);
        $grid->column('updated_at', __('Ngày cập nhật'))->display($dateFormatter);
        $grid->fixColumns(0, 0);
        $grid->disableExport();
       
        
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
        $show = new Show(EduStudent::findOrFail($id));
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');

        $show->field('branch.name', __('Tên chi nhánh'));
        $show->field('class.name', __('Tên lớp'));
        $show->field('channel', __('Kênh'));
        $show->field('wom', __('WOM'));
        $show->field('source', __('Nguồn'));
        $show->field('name', __('Tên học sinh'));
        $show->field('parent', __('Bố mẹ'));
        $show->field('phone_number', __('Số điện thoại'));
        $show->field('last_call', __('Liên lạc gần nhất'));
        $show->field('contact_status', __('Trạng thái liên lạc'));
        $show->field('grade', __('Khối'));
        $show->field('location', __('Địa chỉ'));
        $show->field('school', __('Trường'));
        $show->field('status', __('Trạng thái'))->as(function ($value) use ($status) {
            return $status[$value] ?? '';
        });
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
        $branchesBiz = Branch::where('business_id', Admin::user()->business_id)->pluck('branch_name', 'id');
        $status = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Status")->pluck('description_vi','value');
        $school = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "School")->pluck('description_vi','value');
        $channel = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Channel")->pluck('description_vi','value');
        $wom = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "WOM")->pluck('description_vi','value');
        $grade = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Grade")->pluck('description_vi','value');
        $location = CommonCode::where('business_id', Admin::user()->business_id)->where("type", "Location")->pluck('description_vi','value');
        
        $form = new Form(new EduStudent());
        $form->divider('1. Thông tin cơ bản');
        $form->hidden('business_id')->value($business->id);
        $form->select('branch_id', __('Tên chi nhánh'))->options($branchesBiz)->required();
        if ($form->isEditing()) {
            $classList = EduClass::where('branch_id', Admin::user()->business_id)->where('status', 1)->pluck('name', 'id')->toArray();
            $editingClassId = $form->model()->class_id;
            dd($editingClassId);
            if ($editingClassId && !array_key_exists($editingClassId, $classList)) {
                $class = EduClass::find($editingClassId);
                if ($class) {
                    $classList[$editingClassId] = $class->name;
                }
            }
            $form->select('class_id', __('Tên lớp học'))->options($classList)->required();
        } else {
            $form->select('class_id', __('Tên lớp học'))->options()->required();
        }
        $form->divider('2. Thông tin học sinh');
        $form->select('channel', __('Kênh'))->options($channel);
        $form->select('wom', __('WOM'))->options($wom);
        $form->text('source', __('Nguồn'));
        $form->text('name', __('Tên học sinh'));
        $form->text('parent', __('Bố mẹ'));
        $form->mobile('phone_number', __('Số điện thoại'))->options(['mask' => '999 999 9999']);
        $form->text('last_call', __('Liên lạc gần nhất'));
        $form->text('contact_status', __('Trạng thái liên lạc'));
        $form->select('grade', __('Khối'))->options($grade);
        $form->select('location', __('Địa chỉ'))->options($location);
        $form->select('school', __('Trường'))->options($school);
        $form->select('status', __('Trạng thái'))->options($status);
      

        $urlClass = 'https://business.metaverse-solution.vn/api/class';

        $script = <<<EOT
        $(function() {
            var branchSelect = $(".branch_id");
            var classSelect = $(".class_id");
            var optionsClass = {};
            branchSelect.on('change', function() {
                classSelect.empty();
                optionsClass = {};
                var selectedBranchId = $(this).val();
                if(!selectedBranchId) return
                $.get("$urlClass", { branch_id: selectedBranchId }, function (classes) {
                    var classesActive = classes.filter(function (cls) {
                        return cls.status === 1;
                    });                    
                    $.each(classesActive, function (index, cls) {
                        optionsClass[cls.id] = cls.name;
                    });
                    classSelect.empty();
                    classSelect.append($('<option>', {
                        value: '',
                        text: ''
                    }));
                    $.each(optionsClass, function (id, className) {
                        classSelect.append($('<option>', {
                            value: id,
                            text: className
                        }));
                    });
                    classSelect.trigger('change');
                });
            });
        });
        
        EOT;
        Admin::script($script);
        return $form;
    }
}