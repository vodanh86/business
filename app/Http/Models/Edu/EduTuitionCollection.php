<?php

namespace App\Http\Models\Edu;

use Illuminate\Database\Eloquent\Model;

class EduTuitionCollection extends Model
{
    protected $table = 'edu_tuition_collection';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function company()
    {
        return $this->belongsTo(Branch::class, 'business_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
