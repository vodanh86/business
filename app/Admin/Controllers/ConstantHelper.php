<?php

namespace App\Admin\Controllers;

use Carbon\Carbon;

class ConstantHelper
{
    public static function dateFormatter($dateIn)
    {
        $carbonDateIn = Carbon::parse($dateIn);
        return $dateIn === null ? "" : $carbonDateIn->format('d/m/Y - H:i:s');
    }
    public static function moneyFormatter($money)
    {
        return number_format($money, 0, ',', ' ') . " VND";
    }
    public static function transactionRecordStatus($value)
    {
        if (array_key_exists($value, Constant::RECORD_STATUS)) {
            return Constant::RECORD_STATUS[$value];
        } else {
            return '';
        }
    }
}
