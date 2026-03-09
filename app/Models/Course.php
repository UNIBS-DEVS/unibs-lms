<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'category',
        'study_material_path',
        'status',
    ];

    // App\Models\Course.php
    public function topics()
    {
        return $this->hasMany(CourseTopic::class);
    }

    public function batches()
    {
        return $this->belongsToMany(
            Batch::class,
            'batch_courses',
            'course_id',
            'batch_id'
        )->withTimestamps();
    }
}
