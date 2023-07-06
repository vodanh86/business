<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';

    public function company()
    {
        return $this->hasMany(Company::class);
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
