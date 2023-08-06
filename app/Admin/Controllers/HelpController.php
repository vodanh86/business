<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class HelpController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Hỗ trợ')
            ->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $tab = new Tab();
                    $headers = ['Vị trí', 'Thành phần'];
                    $rows = [
                        ['Bên trái trên','Bao gồm logo(tên hệ thống) và nút mở rộng menu.'],
                        ['Bên trái dưới','Bao gồm các menu của doanh nghiệp. Đã được chủ doanh nghiệp phân chia theo vai trò của người dùng.'],
                        ['Bên phải trên cùng','Bao gồm tên&ảnh người dùng và nút tải lại trang.'],
                    ];
                    $table = new Table($headers, $rows);
                    $tab->add('Hướng dẫn sử dụng', $table);
                    $tab->add('Báo cáo sự cố', 'Liên hệ hỗ trợ email: ...');
                    $column->append($tab);
                });
            });
    }
}
