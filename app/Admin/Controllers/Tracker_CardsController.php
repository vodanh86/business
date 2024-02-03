<?php

namespace App\Admin\Controllers;

use App\Http\Models\Tracker\TrackerCards;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;

class Tracker_CardsController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Thẻ';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TrackerCards());
        $grid->column('id', __('ID'))->filter();
        $grid->column('trello_id', __('ID trello'))->filter();
        $grid->column('name', __('Tên'))->filter();
        $grid->column('phoneNumber', __('Phone Number'))->filter()->sortable();
        $grid->column('customer', __('Khách hàng'))->filter()->sortable();
        $grid->column('customerAddress', __('Địa chỉ'))->filter()->sortable();
        $grid->column('desc', __('Mô tả'))->filter();
        $grid->column('shortUrl', __('Đường dẫn rút gọn'))->filter()->link();
        $grid->column('dateLastActivity', __('Ngày cuối cùng hoạt động'))->filter();
        $grid->fixColumns(0, 0);

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('card_id', __('ID thẻ'));
            $filter->equal('trello_id', __('ID trello'));
            $filter->equal('address', __('Địa chỉ'));
            $filter->equal('badges', __('Huy hiệu'));
            $filter->equal('checkItemStates', __('Trạng thái thẻ'));
            $filter->equal('closed', __('Trạng thái đóng'));
            $filter->equal('coordinates', __('Toạ độ'));
            $filter->equal('creationMethod', __('Phương thức tạo'));
            $filter->equal('dueComplete', __('Lý do hoàn thành'));
            $filter->date('dateLastActivity', __('Ngày cuối cùng hoạt động'));
            $filter->equal('desc', __('Mô tả'));
            $filter->equal('descData', __('Dữ liệu mô tả'));
            $filter->equal('due', __('Lý do'));
            $filter->equal('dueReminder', __('Nhắc nhở đúng hạn'));
            $filter->equal('email', __('Email'));
            $filter->equal('idBoard', __('ID hội đồng'));
            $filter->equal('idChecklists', __('ID Trạng thái danh sách'));
            $filter->equal('idLabels', __('ID nhãn'));
            $filter->equal('idList', __('ID Danh sách'));
            $filter->equal('idMembers', __('ID thành viên'));
            $filter->equal('idMembersVoted', __('ID thành viên đã vote'));
            $filter->equal('idOrganization', __('ID tổ chức'));
            $filter->equal('idShort', __('ID shorrt'));
            $filter->equal('idAttachmentCover', __('ID bìa đính kèm'));
            $filter->equal('labels', __('Nhãn'));
            $filter->equal('limits', __('Giới hạn'));
            $filter->equal('locationName', __('Tên vị trí'));
            $filter->equal('manualCoverAttachment', __('Cẩm nang đính kèm'));
            $filter->equal('name', __('Tên'));
            $filter->equal('nodeId', __('ID nhánh'));
            $filter->equal('pos', __('pos'));
            $filter->equal('shortLink', __('Link rút gọn'));
            $filter->equal('shortUrl', __('Đường dẫn rút gọn'));
            $filter->equal('staticMapUrl', __('Đường dẫn bản đồ tĩnh'));
            $filter->equal('start', __('Bắt đầu'));
            $filter->equal('subscribed', __('Đăng ký'));
            $filter->equal('url', __('Đường dẫn'));
            $filter->equal('cover', __('Cover'));
            $filter->equal('isTemplate', __('Trạng thái mẫu'));
            $filter->equal('cardRole', __('Vai trò thẻ'));
            $filter->equal('attachments', __('Đính kèm'));
            $filter->equal('pluginData', __('Dữ liệu plugin'));
            $filter->equal('customFieldItems', __('Trường tuỳ chỉnh'));
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
        $show = new Show(TrackerCards::findOrFail($id));
        $show->field('card_id', __('ID thẻ'));
        $show->field('trello_id', __('ID trello'));
        $show->field('address', __('Địa chỉ'));
        $show->field('badges', __('Huy hiệu'));
        $show->field('checkItemStates', __('Trạng thái thẻ'));
        $show->field('closed', __('Trạng thái đóng'));
        $show->field('coordinates', __('Toạ độ'));
        $show->field('creationMethod', __('Phương thức tạo'));
        $show->field('dueComplete', __('Lý do hoàn thành'));
        $show->field('dateLastActivity', __('Ngày cuối cùng hoạt động'));
        $show->field('desc', __('Mô tả'));
        $show->field('descData', __('Dữ liệu mô tả'));
        $show->field('due', __('Lý do'));
        $show->field('dueReminder', __('Nhắc nhở đúng hạn'));
        $show->field('email', __('Email'));
        $show->field('idBoard', __('ID hội đồng'));
        $show->field('idChecklists', __('ID Trạng thái danh sách'));
        $show->field('idLabels', __('ID nhãn'));
        $show->field('idList', __('ID Danh sách'));
        $show->field('idMembers', __('ID thành viên'));
        $show->field('idMembersVoted', __('ID thành viên đã vote'));
        $show->field('idOrganization', __('ID tổ chức'));
        $show->field('idShort', __('ID shorrt'));
        $show->field('idAttachmentCover', __('ID bìa đính kèm'));
        $show->field('labels', __('Nhãn'));
        $show->field('limits', __('Giới hạn'));
        $show->field('locationName', __('Tên vị trí'));
        $show->field('manualCoverAttachment', __('Cẩm nang đính kèm'));
        $show->field('name', __('Tên'));
        $show->field('nodeId', __('ID nhánh'));
        $show->field('pos', __('pos'));
        $show->field('shortLink', __('Link rút gọn'));
        $show->field('shortUrl', __('Đường dẫn rút gọn'));
        $show->field('staticMapUrl', __('Đường dẫn bản đồ tĩnh'));
        $show->field('start', __('Bắt đầu'));
        $show->field('subscribed', __('Đăng ký'));
        $show->field('url', __('Đường dẫn'));
        $show->field('cover', __('Cover'));
        $show->field('isTemplate', __('Trạng thái mẫu'));
        $show->field('cardRole', __('Vai trò thẻ'));
        $show->field('attachments', __('Đính kèm'));
        $show->field('pluginData', __('Dữ liệu plugin'));
        $show->field('customFieldItems', __('Trường tuỳ chỉnh'));
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