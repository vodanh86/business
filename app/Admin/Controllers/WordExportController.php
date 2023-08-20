<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduStudentReport;
use App\Http\Models\Edu\EduStudentReportDetail;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Http\Request;

class WordExportController extends AdminController
{
    function encodeSpecialCharacters($text) {
        $encodedText = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $encodedText;
    }

    public function exportWordData(Request $request)
    {
        $student_report_ids = $request->get('q');
        if (empty($student_report_ids)) {
            return response()->json(['error' => 'Missing student report IDs'], 400);
        }
        $reportOverview = EduStudentReport::where("id", $student_report_ids)->get();
        $dataReportOverview = $reportOverview->map(function ($report) {
            return [
                'type' => CommonCode::where('business_id', 1)->where('group', "Edu")->where('type', "ReportType")->where('value', $report->type)->pluck("description_vi")->first(),
                'report_date' => $report->report_date,
                'lesson_name' => $report->lesson_name,
                'home_work' => $report->home_work,
            ];
        });
        $reportDetails = EduStudentReportDetail::where("student_report_id", $student_report_ids)->get();
        $data = $reportDetails->map(function ($report) {
            return [
                'student_name' => $report->student->name,
                'harkwork' => CommonCode::where('business_id', 1)->where('group', "Edu")->where('type', "Harkwork")->where('value', $report->harkwork)->pluck("description_vi")->first(),
                'last_homework' => CommonCode::where('business_id', 1)->where('group', "Edu")->where('type', "LastHomework")->where('value', $report->last_homework)->pluck("description_vi")->first(),
                'mini_test' => $report->mini_test,
                'comment' => CommonCode::where('business_id', 1)->where('group', "Edu")->where('type', "CommentStudent")->where('value', $report->comment)->pluck("description_vi")->first(),
            ];
        });
        $mergedData = $dataReportOverview->concat($data);
        return response()->json($mergedData);
    }

    public function exportWord(Request $request)
    {
        $data = json_decode($request->input('data'), true);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Loại báo cáo: ' . $this->encodeSpecialCharacters($data[0]['type']));
        $section->addText('Ngày báo cáo: ' . $this->encodeSpecialCharacters($data[0]['report_date']));
        $section->addText('Tên bài giảng: ' . $this->encodeSpecialCharacters($data[0]['lesson_name']));
        $section->addText('Bài tập về nhà: ' . $this->encodeSpecialCharacters($data[0]['home_work']));
        
        $table = $section->addTable();
        $table->addRow();
        $table->addCell(4000)->addText('Tên học sinh');
        $table->addCell(4000)->addText('Chuyên cần');
        $table->addCell(4000)->addText('Bài tập cuối');
        $table->addCell(4000)->addText('Kiểm tra ngắn');
        $table->addCell(4000)->addText('Bình luận');

        foreach (array_slice($data, 1) as $row) {
            $table->addRow();
            $table->addCell(4000)->addText($this->encodeSpecialCharacters($row['student_name']));
            $table->addCell(4000)->addText($this->encodeSpecialCharacters($row['harkwork']));
            $table->addCell(4000)->addText($this->encodeSpecialCharacters($row['last_homework']));
            $table->addCell(4000)->addText($this->encodeSpecialCharacters($row['mini_test']));
            $table->addCell(4000)->addText($this->encodeSpecialCharacters($row['comment']));
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'word_export');
        $phpWord->save($tempFilePath);
        return response()->download($tempFilePath, 'baocaokqhoctap.docx')->deleteFileAfterSend(true);
    }
}
