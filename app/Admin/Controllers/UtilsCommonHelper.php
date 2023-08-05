<?php

namespace App\Admin\Controllers;

use App\Http\Models\Core\Account;
use App\Http\Models\Core\Branch;
use App\Http\Models\Core\Business;
use App\Http\Models\Core\CommonCode;
use App\Http\Models\Core\TransactionCode;
use App\Http\Models\Edu\EduClass;
use App\Http\Models\Edu\EduSchedule;
use App\Http\Models\Edu\EduStudent;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Str;

class UtilsCommonHelper
{
    public static function commonCode($group, $type, $description, $value)
    {
        if ($group === "Core") {
            return CommonCode::where('group', $group)
                ->where('type', $type)
                ->pluck($description, $value);
        } else {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
                ->where('group', $group)
                ->where('type', $type)
                ->pluck($description, $value);
            return $commonCode;
        }
    }
    public static function commonCodeGridFormatter($group, $type, $description, $value)
    {
        $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
            ->where('group', $group)
            ->where('type', $type)
            ->where('value', $value)
            ->first();
        return $commonCode ? $commonCode->$description : '';
    }
    //Kiem tra ten lai(doi lai)
    public static function statusFormatter($value, $group, $isGrid)
    {   
        $result = $value ? $value : 0;
        if ($group === "Core") {
            $commonCode = CommonCode::where('group', $group)
                ->where('type', 'Status')
                ->where('value', $result)
                ->first();
        } else {
            $commonCode = CommonCode::where('business_id', Admin::user()->business_id)
                ->where('group', $group)
                ->where('type', 'Status')
                ->where('value', $result)
                ->first();
        }
        if ($commonCode && $isGrid === "grid") {
            return $result === 1 ? "<span class='label label-success'>$commonCode->description_vi</span>" : "<span class='label label-danger'>$commonCode->description_vi</span>";
        }
        return $commonCode->description_vi;
    }
    public static function statusFormFormatter()
    {
        return self::commonCode("Core", "Status", "description_vi", "value");
    }
    public static function statusGridFormatter($status)
    {
        return self::statusFormatter($status, "Core", "grid");
    }
    public static function statusDetailFormatter($status)
    {
        return self::statusFormatter($status, "Core", "detail");
    }


    public static function bankAccountFormatter($accountNumber, $isGrid)
    {
        $bankAccount =  Account::where('id', $accountNumber)->first();
        if ($bankAccount && $isGrid === "grid") {
            return "<span class='badge badge-primary'>$bankAccount->bank_name - $bankAccount->number</span>";
        } else if ($bankAccount) {
            return "$bankAccount->bank_name - $bankAccount->number";
        } else {
            return "";
        }
    }
    public static function bankAccountGridFormatter($accountNumber)
    {
        return self::bankAccountFormatter($accountNumber, "grid");
    }
    public static function bankAccountDetailFormatter($accountNumber)
    {
        return self::bankAccountFormatter($accountNumber, "detail");
    }
    public static function bankAccountFormFormatter()
    {
        $bankAccounts = Account::where('business_id', Admin::user()->business_id)->where('status', 1)->get();
        $bankAccountOptions = $bankAccounts->map(function ($account) {
            return [
                'value' => $account->id,
                'text' => $account->bank_name . ' - ' . $account->number,
            ];
        })->pluck('text', 'value');
        return $bankAccountOptions;
    }
    public static function transactionCodeFormatter($id, $isGrid)
    {
        $transactionCode =  TransactionCode::where('id', $id)->first();
        if ($transactionCode && $isGrid === "grid") {
            $debitCreditInd = $transactionCode->debit_credit_ind ?? "";
            $name = $transactionCode->name ?? "";
            return "<span class='label label-primary'>$debitCreditInd - $name</span>";
        } else if ($transactionCode && $isGrid === "detail") {
            return "$transactionCode->debit_credit_ind - $transactionCode->name";
        } else {
            return "";
        }
    }
    public static function transactionCodeFormFormatter($type)
    {
        $transactionCodes = $type ? TransactionCode::where('debit_credit_ind', $type)->where("status", 1)->get() : TransactionCode::all()->where("status", 1);
        $transactionCodeOptions = $transactionCodes->map(function ($code) {
            return [
                'value' => $code->id,
                'text' => $code->debit_credit_ind . ' - ' . $code->name,
            ];
        })->pluck('text', 'value');
        return $transactionCodeOptions;
    }

    public static function currentBusiness()
    {
        return Business::where('id', Admin::user()->business_id)->first();
    }
    public static function optionsBranch()
    {
        return Branch::where('business_id', Admin::user()->business_id)->where('status', 1)->pluck('branch_name', 'id');
    }
    public static function optionsClassByBranchId($branchId)
    {
        if ($branchId !== null) {
            return EduClass::where("branch_id", $branchId)->pluck('name', 'id');
        }
        return EduClass::all()->pluck('name', 'id');
    }
    public static function optionsScheduleByBranchId($branchId)
    {
        if ($branchId !== null) {
            return EduSchedule::where("branch_id", $branchId)->where('status', 1)->pluck('name', 'id');
        }
        return EduSchedule::all()->where('status', 1)->pluck('name', 'id');
    }
    public static function optionsStudentByScheduleId($scheduleId)
    {
        if ($scheduleId !== null) {
            return EduStudent::where("schedule_id", $scheduleId)->where('status', 1)->pluck('name', 'id');
        }
        return EduStudent::all()->where('status', 1)->pluck('name', 'id');
    }
    public static function generateTransactionId($type)
    {
        $today = date("ymd");
        $currentTime = Carbon::now('Asia/Bangkok');
        $time = $currentTime->format('His');
        $userId = Str::padLeft(Admin::user()->id, 6, '0');
        $code = $type . $today . $userId . $time;
        return $code;
    }
}
