<?php

namespace App\Http\Models\Edu;

use Illuminate\Database\Eloquent\Model;

class EduStudent extends Model
{
    protected $table = 'edu_student';

    public function class()
    {
        return $this->belongsTo(EduClass::class, 'class_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
