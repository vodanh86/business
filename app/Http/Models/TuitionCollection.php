<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionCollection extends Model
{
    protected $table = 'tuition_collection';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'business_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
