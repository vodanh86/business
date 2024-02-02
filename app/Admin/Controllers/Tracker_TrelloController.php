<?php

namespace App\Admin\Controllers;

use App\Http\Models\Tracker\TrackerTrello;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;

class Tracker_TrelloController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Trello';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TrackerTrello());
        $grid->column('trello_id', __('ID Trello'));
        $grid->column('nodeId', __('ID nhánh'));
        $grid->column('idOrganization', __('ID tổ chức'));
        $grid->column('idEnterprise', __('ID doanh nghiệp'));
        $grid->column('name', __('Tên'));
        $grid->column('desc', __('Mô tả'));
        $grid->column('descData', __('Dữ liệu mô tả'));
        $grid->column('closed', __('Trạng thái đóng'));
        $grid->column('dateClosed', __('Trạng thái ngày đóng'));
        $grid->column('limits', __('Giới hạn'));
        $grid->column('pinned', __('Trạng thái ghim'));
        $grid->column('starred', __('Trạng thái starred'));
        $grid->column('url', __('Đường dẫn trello'));
        $grid->column('prefs', __('prefs'));
        $grid->column('shortLink', __('Link rút gọn'));
        $grid->column('subscribed', __('Đăng ký'));
        $grid->column('labelNames', __('Tên nhãn'));
        $grid->column('powerUps', __('powerUps'));
        $grid->column('dateLastActivity', __('Ngày hoạt động cuối cùng'));
        $grid->column('dateLastView', __('Ngày xem cuối cùng'));
        $grid->column('shortUrl', __('Đường dẫn rút gọn'));
        $grid->column('idTags', __('ID tag'));
        $grid->column('datePluginDisable', __('datePluginDisable'));
        $grid->column('creationMethod', __('Phương thức tạo'));
        $grid->column('ixUpdate', __('ixUpdate'));
        $grid->column('templateGallery', __('Bộ sưu tập mẫu'));
        $grid->column('enterpriseOwned', __('Doanh nghiệp sở hữu'));
        $grid->column('idBoardSource', __('ID nguồn hội đồng quản trị'));
        $grid->column('premiumFeatures', __('Tính năng cao cấp'));
        $grid->column('idMemberCreator', __('ID người tạo'));
        $grid->column('labels', __('Nhãn'));
        $grid->column('lists', __('Danh sách'));
        $grid->column('members', __('Thành viên'));
        $grid->column('checklists', __('Trạng thái danh sách'));
        $grid->column('customFields', __('Trường tuỳ chỉnh'));
        $grid->column('memberships', __('Hội viện'));
        $grid->column('pluginData', __('Dữ liệu hội viên'));
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
        $show = new Show(TrackerTrello::findOrFail($id));
        $show->field('trello_id', __('ID Trello'));
        $show->field('nodeId', __('ID nhánh'));
        $show->field('idOrganization', __('ID tổ chức'));
        $show->field('idEnterprise', __('ID doanh nghiệp'));
        $show->field('name', __('Tên'));
        $show->field('desc', __('Mô tả'));
        $show->field('descData', __('Dữ liệu mô tả'));
        $show->field('closed', __('Trạng thái đóng'));
        $show->field('dateClosed', __('Trạng thái ngày đóng'));
        $show->field('limits', __('Giới hạn'));
        $show->field('pinned', __('Trạng thái ghim'));
        $show->field('starred', __('Trạng thái starred'));
        $show->field('url', __('Đường dẫn trello'));
        $show->field('prefs', __('prefs'));
        $show->field('shortLink', __('Link rút gọn'));
        $show->field('subscribed', __('Đăng ký'));
        $show->field('labelNames', __('Tên nhãn'));
        $show->field('powerUps', __('powerUps'));
        $show->field('dateLastActivity', __('Ngày hoạt động cuối cùng'));
        $show->field('dateLastView', __('Ngày xem cuối cùng'));
        $show->field('shortUrl', __('Đường dẫn rút gọn'));
        $show->field('idTags', __('ID tag'));
        $show->field('datePluginDisable', __('datePluginDisable'));
        $show->field('creationMethod', __('Phương thức tạo'));
        $show->field('ixUpdate', __('ixUpdate'));
        $show->field('templateGallery', __('Bộ sưu tập mẫu'));
        $show->field('enterpriseOwned', __('Doanh nghiệp sở hữu'));
        $show->field('idBoardSource', __('ID nguồn hội đồng quản trị'));
        $show->field('premiumFeatures', __('Tính năng cao cấp'));
        $show->field('idMemberCreator', __('ID người tạo'));
        $show->field('labels', __('Nhãn'));
        $show->field('lists', __('Danh sách'));
        $show->field('members', __('Thành viên'));
        $show->field('checklists', __('Trạng thái danh sách'));
        $show->field('customFields', __('Trường tuỳ chỉnh'));
        $show->field('memberships', __('Hội viện'));
        $show->field('pluginData', __('Dữ liệu hội viên'));

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