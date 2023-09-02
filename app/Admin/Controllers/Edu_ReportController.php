<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Edu_AttendanceReport;
use App\Admin\Forms\Edu_CashFlowStatementReport;
use App\Admin\Forms\Edu_DetailStudentReport;
use App\Admin\Forms\Edu_RevenueReport;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Controllers\AdminController;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Edu_ReportController extends AdminController
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
    public function cashflowStatement(Content $content)
    {
        $content
            ->title('Báo cáo dòng tiền mặt')
            ->row(new Edu_CashFlowStatementReport());
        if ($data = session('result')) {
            if ($data["type"] == "all") {
                $results = DB::select("call edu_cashflow_statement(?, ?)", [$data["from_date"], $data["to_date"]]);
                $headers = ['Báo cáo tháng', 'Dòng tiền vào', 'Dòng tiền ra'];
                $rows = [];
                foreach ($results as $item => $row) {
                    $formattedCashIn = ConstantHelper::moneyFormatter($row->total_in);
                    $formattedCashOut = ConstantHelper::moneyFormatter($row->total_out);
                    $rows[] = [
                        $row->month_rpt,
                        $formattedCashIn,
                        $formattedCashOut
                    ];
                }
            } else if ($data["type"] == "in") {
                $results = DB::select("call edu_cashin_statement(?, ?)", [$data["from_date"], $data["to_date"]]);
                $sum = [0, 0];
                foreach ($results as $i => $row) {
                    $sum[0] = $i + 1;
                    $sum[1] += $row->value;
                }
                $headers = ['Tên lịch học', 'Tên học sinh', 'Ngày nộp tiền', 'Số lượng', 'Đơn giá', 'Giá trị', 'Mô tả'];
                $rows = [];
                foreach ($results as $item => $row) {
                    $formattedProcessingDate = ConstantHelper::dayFormatter($row->processing_date);
                    $formattedUnitPrice = ConstantHelper::moneyFormatter($row->unit_price);
                    $formattedValue = ConstantHelper::moneyFormatter($row->value);
                    $rows[] = [
                        $row->schedule_name,
                        $row->student_name,
                        $formattedProcessingDate,
                        $row->amount,
                        $formattedUnitPrice,
                        $formattedValue,
                        $row->description,
                    ];
                }
                $rows[] = ["", "", "", "", "", 'Tổng cộng: ' . number_format($sum[1]) . ' VND', ""];
            } else if ($data["type"] == "out") {
                $results = DB::select("call edu_cashout_statement(?, ?)", [$data["from_date"], $data["to_date"]]);
                $sum = [0, 0];
                foreach ($results as $i => $row) {
                    $sum[0] = $i + 1;
                    $sum[1] += $row->amount;
                }
                $headers = ['Tên chi phí', 'Loại chi phí', 'Nhóm chi phí', 'Số tiền', 'Ngày thực hiện', 'Mô tả'];
                $rows = [];
                foreach ($results as $item => $row) {
                    $formattedAmount = ConstantHelper::moneyFormatter($row->amount);
                    $formattedValueDate = ConstantHelper::dayFormatter($row->value_date);
                    $rows[] = [
                        $row->expense_name,
                        $row->expense_type,
                        $row->expense_group,
                        $formattedAmount,
                        $formattedValueDate,
                        $row->description,
                    ];
                }
                $rows[] = ["", "", "", 'Tổng cộng: ' . number_format($sum[1]) . ' VND', "", ""];
            }
            $table = new Table($headers, $rows);
            $tab = new Tab();

            // store in excel
            array_unshift($rows, $headers);
            $export = new ReportExport($rows);
            Excel::store($export, 'public/files/report.xlsx');

            $tab->add('Kết quả', "<b>Từ ngày: </b>" . $data['from_date'] . " <b> Đến ngày: </b> " . $data["to_date"] .
                "<br/>Link download: <a href='" . env('APP_URL') . "/storage/files/report.xlsx' target='_blank'>Link</a><br/><div class='report-result'>" . $table . '</div>');
            $content->row($tab);
        }

        return $content;
    }
    public function eduReport(Content $content)
    {
        $content
            ->title('Báo cáo doanh thu thực hiện')
            ->row(new Edu_RevenueReport());

        if ($data = session('result')) {

            if ($data["type"] == "l") {
                $results = DB::select("call RevenueBySchedule(?, ?)", [$data["from_date"], $data["to_date"]]);
                $sum = [0, 0];
                foreach ($results as $i => $row) {
                    $sum[0] = $i + 1;
                    $sum[1] += $row->total;
                }
                $headers = ['Tên lớp', 'Tiền thu được'];
                $rows = [];
                foreach ($results as $item => $row) {
                    $formattedMoneyTotal = ConstantHelper::moneyFormatter($row->total);
                    $rows[] = [
                        $row->name,
                        $formattedMoneyTotal
                    ];
                }
                $rows[] = ["", 'Tổng cộng: ' . number_format($sum[1]) . ' VND'];
            } else {
                $results = DB::select("call RevenueDetail(?, ?)", [$data["from_date"], $data["to_date"]]);
                $sum = [0, 0];
                foreach ($results as $i => $row) {
                    $sum[0] = $i + 1;
                    $sum[1] += $row->amount;
                }
                $headers = ['ID học sinh', 'Tên lớp học', 'Tên học sinh', 'Số buổi đi học', 'Số tiền một buổi', 'Tổng tiền'];
                $rows = [];
                foreach ($results as $student => $row) {
                    $formattedUnitPrice = ConstantHelper::moneyFormatter($row->unit_price);
                    $formattedAmount = ConstantHelper::moneyFormatter($row->amount);
                    $rows[] = [
                        $row->student_id,
                        $row->schedule_name,
                        $row->student_name,
                        $row->cnt,
                        $formattedUnitPrice,
                        $formattedAmount
                    ];
                }
                $rows[] = ["Tổng cộng: $sum[0]", "", "", "", "", number_format($sum[1]) . ' VND'];
            }

            $table = new Table($headers, $rows);
            $tab = new Tab();

            // store in excel
            array_unshift($rows, $headers);
            $export = new ReportExport($rows);
            Excel::store($export, 'public/files/report.xlsx');

            $tab->add('Kết quả', "<b>Từ ngày: </b>" . $data['from_date'] . " <b> Đến ngày: </b> " . $data["to_date"] .
                "<br/>Link download: <a href='" . env('APP_URL') . "/storage/files/report.xlsx' target='_blank'>Link</a><br/><div class='report-result'>" . $table . '</div>');
            $content->row($tab);
        }

        return $content;
    }
    public function attendanceReport(Content $content)
    {
        $content
            ->title('Báo cáo học tập&điểm danh')
            ->row(new Edu_AttendanceReport());
        if ($data = session('result')) {
            $results = DB::select("call edu_attendance_report(?, ?)", [$data["from_date"], $data["to_date"]]);
            $headers = ['Tháng báo cáo', 'Tên lớp học', 'ID học sinh', 'Tên học sinh', 'Đúng giờ', 'Đến muộn', 'Nghỉ học', 'Có làm', 'Làm thiếu', 'Không làm', 'Ý thức tốt', 'Làm việc riêng', 'Ko tương tác hoạt động lớp'];
            $rows = [];
            foreach ($results as $item => $row) {
                $rows[] = [
                            $row->month_report,
                            $row->schedule_name,
                            $row->student_id,
                            $row->student_name,
                            $row->on_time,
                            $row->late,
                            $row->absent,
                            $row->done,
                            $row->in_complete,
                            $row->not_done,
                            $row->good_attitude,
                            $row->individual_work,
                            $row->not_interact_with_class,
                ];
            }
            $table = new Table($headers, $rows);
            $tab = new Tab();

            // store in excel
            array_unshift($rows, $headers);
            $export = new ReportExport($rows);
            Excel::store($export, 'public/files/report.xlsx');

            $tab->add(
                'Kết quả',
                "<br/>Link download: <a href='" . env('APP_URL') . "/storage/files/report.xlsx' target='_blank'>Link</a><br/><div class='report-result'>" . $table . '</div>'
            );
            $content->row($tab);
        }
        return $content;
    }
    public function detailStudentReport(Content $content)
    {
        $content
            ->title('Báo cáo chi tiết học sinh')
            ->row(new Edu_DetailStudentReport());
        if ($data = session('result')) {
            if ($data["type"] == "detail") {
                $results = DB::select("call student_report_detail(?)", [$data["student_id"]]);
                $headers = ['Tên lịch học', 'Tên học sinh', 'Ngày báo cáo', 'Làm việc chăm chỉ', 'Bài tập về nhà', 'Bài kiểm tra ngắn', 'Bình luận', 'Đơn giá'];
                $rows = [];
                foreach ($results as $item => $row) {
                    $formattedReportDate = ConstantHelper::dayFormatter($row->report_date);
                    $formattedUnitRrice = ConstantHelper::moneyFormatter($row->unit_price);
                    $iterationIndex = $item + 1;
                    $rows[] = [
                        $row->schedule_name,
                        $row->student_name,
                        $formattedReportDate,
                        $row->harkwork,
                        $row->last_homework,
                        $row->mini_test,
                        $row->comment,
                        $formattedUnitRrice
                    ];
                }
                $rows[] = ["", "", 'Tổng cộng buổi học: ' . $iterationIndex, "", "", "", "", ""];
            } else if ($data["type"] == "tuition_collection") {
                $results = DB::select("call edu_tuition_collection_by_student(?)", [$data["student_id"]]);
                $headers = ['Tên lịch học', 'Tên học sinh', 'Ngày nộp tiền', 'Số buổi', 'Đơn giá', 'Giá trị', 'Mô tả'];
                $rows = [];
                foreach ($results as $item => $row) {
                    $formattedProcessingDate = ConstantHelper::dayFormatter($row->processing_date);
                    $formattedUnitRrice = ConstantHelper::moneyFormatter($row->unit_price);
                    $formattedValue = ConstantHelper::moneyFormatter($row->value);
                    $rows[] = [
                        $row->schedule_name,
                        $row->student_name,
                        $formattedProcessingDate,
                        $row->amount,
                        $formattedUnitRrice,
                        $formattedValue,
                        $row->description,
                    ];
                }
            }
            $table = new Table($headers, $rows);
            $tab = new Tab();

            // store in excel
            array_unshift($rows, $headers);
            $export = new ReportExport($rows);
            Excel::store($export, 'public/files/report.xlsx');

            $tab->add(
                'Kết quả',
                "<br/>Link download: <a href='" . env('APP_URL') . "/storage/files/report.xlsx' target='_blank'>Link</a><br/><div class='report-result'>" . $table . '</div>'
            );
            $content->row($tab);
        }
        return $content;
    }
}