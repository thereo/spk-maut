<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCriterionValue extends Model
{
    protected $fillable = ['employee_id', 'criterion_id', 'batch_id', 'value'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
