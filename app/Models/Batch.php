<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['name', 'description'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withTimestamps();
    }
}
