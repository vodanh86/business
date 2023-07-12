<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessClass extends Model
{
    protected $table = 'class';

    public function company()
    {
        return $this->hasMany(Company::class);
    }
    public function classes()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
	protected $hidden = [
    ];

	protected $guarded = [];
}
