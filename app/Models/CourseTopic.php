<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseTopic extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'description', 'remark'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
