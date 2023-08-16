<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\EduReport;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Controllers\AdminController;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Báo cáo';

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function eduReport(Content $content)
    {
        $content
            ->title('Báo cáo')
            ->row(new EduReport());

        if ($data = session('result')) {
            
            if ($data["type"] == "l") {
            } else {
                $results = DB::select("call RevenueDetail(?, ?)", [$data["from_date"], $data["to_date"]]);

                $sum = [0, 0];
                foreach ($results as $i => $row) {
                    $sum[0] = $i + 1;
                    $sum[1] += $row->amount;
                }
                $headers = ['Số thứ tự', 'Tên học sinh', 'Số buổi đi học', 'Số tiền một buổi', 'Tổng tiền'];
                $rows = [];
                foreach ($results as $student => $row) {
                    $formattedUnitPrice = ConstantHelper::moneyFormatter($row->unit_price);
                    $formattedAmount = ConstantHelper::moneyFormatter($row->amount);
                    $iterationIndex = $student + 1;
                    $rows[] = [
                        $iterationIndex,
                        $row->name, 
                        $row->cnt, 
                        $formattedUnitPrice,
                        $formattedAmount
                    ];
                }
                $rows[] = ["Tổng cộng: $sum[0]", "", "", "", number_format($sum[1]) . ' VND'];
            }

            $table = new Table($headers, $rows);
            $tab = new Tab();

            // store in excel
            array_unshift($rows, $headers);
            $export = new ReportExport($rows);
            Excel::store($export, 'public/files/report.xlsx');

            $tab->add('Kết quả', "<b>Từ ngày: </b>" . $data['from_date'] . " <b> Đến ngày: </b> " . $data["to_date"] .
                "<br/>Link download: <a href='" . env('APP_URL') . "/storage/files/report.xlsx' target='_blank'>Link</a><br/><div class='report-result'>" . $table.'</div>');
            $content->row($tab);
        }

        return $content;
    }

}
