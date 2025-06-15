<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'position', 'department'];

    public function batches()
    {
        return $this->belongsToMany(Batch::class)->withTimestamps();
    }

    public function criterionValues()
    {
        return $this->hasMany(\App\Models\EmployeeCriterionValue::class);
    }
}
