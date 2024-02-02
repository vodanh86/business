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
        $grid->column('trello_id', __('ID Trello'))->filter();
        $grid->column('nodeId', __('ID nhánh'))->filter();
        $grid->column('idOrganization', __('ID tổ chức'))->filter();
        $grid->column('idEnterprise', __('ID doanh nghiệp'))->filter();
        $grid->column('name', __('Tên'))->filter();
        $grid->column('desc', __('Mô tả'))->filter();
        $grid->column('descData', __('Dữ liệu mô tả'))->filter();
        $grid->column('closed', __('Trạng thái đóng'))->filter();
        $grid->column('dateClosed', __('Trạng thái ngày đóng'))->filter();
        $grid->column('limits', __('Giới hạn'))->filter();
        $grid->column('pinned', __('Trạng thái ghim'))->filter();
        $grid->column('starred', __('Trạng thái starred'))->filter();
        $grid->column('url', __('Đường dẫn trello'))->filter();
        $grid->column('prefs', __('prefs'))->filter();
        $grid->column('shortLink', __('Link rút gọn'))->filter();
        $grid->column('subscribed', __('Đăng ký'))->filter();
        $grid->column('labelNames', __('Tên nhãn'))->filter();
        $grid->column('powerUps', __('powerUps'))->filter();
        $grid->column('dateLastActivity', __('Ngày hoạt động cuối cùng'))->filter();
        $grid->column('dateLastView', __('Ngày xem cuối cùng'))->filter();
        $grid->column('shortUrl', __('Đường dẫn rút gọn'))->filter();
        $grid->column('idTags', __('ID tag'))->filter();
        $grid->column('datePluginDisable', __('datePluginDisable'))->filter();
        $grid->column('creationMethod', __('Phương thức tạo'))->filter();
        $grid->column('ixUpdate', __('ixUpdate'))->filter();
        $grid->column('templateGallery', __('Bộ sưu tập mẫu'))->filter();
        $grid->column('enterpriseOwned', __('Doanh nghiệp sở hữu'))->filter();
        $grid->column('idBoardSource', __('ID nguồn hội đồng quản trị'))->filter();
        $grid->column('premiumFeatures', __('Tính năng cao cấp'))->filter();
        $grid->column('idMemberCreator', __('ID người tạo'))->filter();
        $grid->column('labels', __('Nhãn'))->filter();
        $grid->column('lists', __('Danh sách'))->filter();
        $grid->column('members', __('Thành viên'))->filter();
        $grid->column('checklists', __('Trạng thái danh sách'))->filter();
        $grid->column('customFields', __('Trường tuỳ chỉnh'))->filter();
        $grid->column('memberships', __('Hội viện'))->filter();
        $grid->column('pluginData', __('Dữ liệu hội viên'))->filter();
        $grid->fixColumns(0, 0);

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('trello_id', __('ID Trello'));
            $filter->equal('nodeId', __('ID nhánh'));
            $filter->equal('idOrganization', __('ID tổ chức'));
            $filter->equal('idEnterprise', __('ID doanh nghiệp'));
            $filter->equal('name', __('Tên'));
            $filter->equal('desc', __('Mô tả'));
            $filter->equal('descData', __('Dữ liệu mô tả'));
            $filter->equal('closed', __('Trạng thái đóng'));
            $filter->date('dateClosed', __('Ngày đóng'));
            $filter->equal('limits', __('Giới hạn'));
            $filter->equal('pinned', __('Trạng thái ghim'));
            $filter->equal('starred', __('Trạng thái starred'));
            $filter->equal('url', __('Đường dẫn trello'));
            $filter->equal('prefs', __('prefs'));
            $filter->equal('shortLink', __('Link rút gọn'));
            $filter->equal('subscribed', __('Đăng ký'));
            $filter->equal('labelNames', __('Tên nhãn'));
            $filter->equal('powerUps', __('powerUps'));
            $filter->date('dateLastActivity', __('Ngày hoạt động cuối cùng'));
            $filter->date('dateLastView', __('Ngày xem cuối cùng'));
            $filter->equal('shortUrl', __('Đường dẫn rút gọn'));
            $filter->equal('idTags', __('ID tag'));
            $filter->date('datePluginDisable', __('datePluginDisable'));
            $filter->equal('creationMethod', __('Phương thức tạo'));
            $filter->equal('ixUpdate', __('ixUpdate'));
            $filter->equal('templateGallery', __('Bộ sưu tập mẫu'));
            $filter->equal('enterpriseOwned', __('Doanh nghiệp sở hữu'));
            $filter->equal('idBoardSource', __('ID nguồn hội đồng quản trị'));
            $filter->equal('premiumFeatures', __('Tính năng cao cấp'));
            $filter->equal('idMemberCreator', __('ID người tạo'));
            $filter->equal('labels', __('Nhãn'));
            $filter->equal('lists', __('Danh sách'));
            $filter->equal('members', __('Thành viên'));
            $filter->equal('checklists', __('Trạng thái danh sách'));
            $filter->equal('customFields', __('Trường tuỳ chỉnh'));
            $filter->equal('memberships', __('Hội viện'));
            $filter->equal('pluginData', __('Dữ liệu hội viên'));
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