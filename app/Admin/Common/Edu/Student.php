<?php

namespace App\Admin\Common\Edu;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Core\CommonCode;

class Student extends Model
{
    /**
     * Get WOM description based on value.
     *
     * @param mixed $value
     * @return string
     */
    public function getWomDescription($value)
    {
        $commonCode = CommonCode::where('business_id', $this->business_id)
            ->where('type', 'WOM')
            ->where('value', $value)
            ->first();
        return $commonCode ? $commonCode->description_vi : '';
    }

    // ...
}
