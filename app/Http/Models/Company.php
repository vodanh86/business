<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    protected $hidden = [
    ];

    protected $guarded = [];
}
