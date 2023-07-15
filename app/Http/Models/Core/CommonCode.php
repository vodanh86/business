<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CommonCode extends Model
{
    protected $table = 'core_common_code';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    protected $hidden = [
    ];

    protected $guarded = [];
}
