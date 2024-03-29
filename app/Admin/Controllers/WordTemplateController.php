<?php

namespace App\Admin\Controllers;

use App\Http\Models\Edu\EduTuitionCollection;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Http\Request;

class WordTemplateController extends AdminController
{
    public function createBill(Request $request)
    {
        $id = $request->input('id');
        $tuitionCollection = EduTuitionCollection::find($id);
        $name = 'HoaDonThuTien' . $tuitionCollection->student->name;
        $document = new \PhpOffice\PhpWord\TemplateProcessor(public_path() . "/template/bill.docx");
        $document->setValue("student_name", $tuitionCollection->student->name);
        $document->setValue("student_phone_number", $tuitionCollection->student->phone_number);
        $document->setValue("processing_date", $tuitionCollection->processing_date);
        $document->setValue("value_date", $tuitionCollection->value_date);
        $document->setValue("next_date", $tuitionCollection->next_date);
        $document->setValue("amount", $tuitionCollection->amount);
        $document->setValue("value", $tuitionCollection->value);
        $document->setValue("unit_price", $tuitionCollection->unit_price);
        $document->setValue("account_id", $tuitionCollection->account_id);
        $outputPath = storage_path("/$name.docx");
        $document->saveAs($outputPath);
        return response()->download($outputPath, "$name.docx");
    }
}