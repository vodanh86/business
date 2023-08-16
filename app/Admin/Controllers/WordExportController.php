<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\CommonCode;
use App\Http\Models\Edu\EduStudentReportDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class WordExportController extends AdminController
{
    public function exportWordData(Request $request)
    {
        $student_report_ids = $request->get('q');
        if (empty($student_report_ids)) {
            return response()->json(['error' => 'Missing student report IDs'], 400);
        }
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

        return response()->json($data);
    }



    public function exportWord(Request $request)
    {
        $data = json_decode($request->input('data'), true);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(4000)->addText('Tên học sinh');
        $table->addCell(4000)->addText('Chuyên cần');
        $table->addCell(4000)->addText('Bài tập cuối');
        $table->addCell(4000)->addText('Kiểm tra ngắn');
        $table->addCell(4000)->addText('Bình luận');

        foreach ($data as $row) {
            $table->addRow();
            $table->addCell(4000)->addText($row['student_name']);
            $table->addCell(4000)->addText($row['harkwork']);
            $table->addCell(4000)->addText($row['last_homework']);
            $table->addCell(4000)->addText($row['mini_test']);
            $table->addCell(4000)->addText($row['comment']);
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'word_export');
        $phpWord->save($tempFilePath);
        return response()->download($tempFilePath, 'baocaokqhoctap.docx')->deleteFileAfterSend(true);
    }
}
