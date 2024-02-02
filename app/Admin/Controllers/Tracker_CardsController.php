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
        $grid->column('card_id', __('ID thẻ'));
        $grid->column('trello_id', __('ID trello'));
        $grid->column('address', __('Địa chỉ'));
        $grid->column('badges', __('Huy hiệu'));
        $grid->column('checkItemStates', __('Trạng thái thẻ'));
        $grid->column('closed', __('Trạng thái đóng'));
        $grid->column('coordinates', __('Toạ độ'));
        $grid->column('creationMethod', __('Phương thức tạo'));
        $grid->column('dueComplete', __('Lý do hoàn thành'));
        $grid->column('dateLastActivity', __('Ngày cuối cùng hoạt động'));
        $grid->column('desc', __('Mô tả'));
        $grid->column('descData', __('Dữ liệu mô tả'));
        $grid->column('due', __('Lý do'));
        $grid->column('dueReminder', __('Nhắc nhở đúng hạn'));
        $grid->column('email', __('Email'));
        $grid->column('idBoard', __('ID hội đồng'));
        $grid->column('idChecklists', __('ID Trạng thái danh sách'));
        $grid->column('idLabels', __('ID nhãn'));
        $grid->column('idList', __('ID Danh sách'));
        $grid->column('idMembers', __('ID thành viên'));
        $grid->column('idMembersVoted', __('ID thành viên đã vote'));
        $grid->column('idOrganization', __('ID tổ chức'));
        $grid->column('idShort', __('ID shorrt'));
        $grid->column('idAttachmentCover', __('ID bìa đính kèm'));
        $grid->column('labels', __('Nhãn'));
        $grid->column('limits', __('Giới hạn'));
        $grid->column('locationName', __('Tên vị trí'));
        $grid->column('manualCoverAttachment', __('Cẩm nang đính kèm'));
        $grid->column('name', __('Tên'));
        $grid->column('nodeId', __('ID nhánh'));
        $grid->column('pos', __('pos'));
        $grid->column('shortLink', __('Link rút gọn'));
        $grid->column('shortUrl', __('Đường dẫn rút gọn'));
        $grid->column('staticMapUrl', __('Đường dẫn bản đồ tĩnh'));
        $grid->column('start', __('Bắt đầu'));
        $grid->column('subscribed', __('Đăng ký'));
        $grid->column('url', __('Đường dẫn'));
        $grid->column('cover', __('Cover'));
        $grid->column('isTemplate', __('Trạng thái mẫu'));
        $grid->column('cardRole', __('Vai trò thẻ'));
        $grid->column('attachments', __('Đính kèm'));
        $grid->column('pluginData', __('Dữ liệu plugin'));
        $grid->column('customFieldItems', __('Trường tuỳ chỉnh'));
        $grid->fixColumns(0, 0);
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