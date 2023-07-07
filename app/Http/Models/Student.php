<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';

    public function class()
    {
        return $this->belongsTo(BusinessClass::class, 'class_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
