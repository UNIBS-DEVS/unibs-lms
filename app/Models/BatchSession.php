<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BatchSession extends Model
{
    protected $fillable = [
        'session_name',
        'batch_id',
        'trainer_id',
        'course_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'location',
        'type',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function attendances()
    {
        return $this->hasMany(SessionAttendance::class, 'session_id'); // fixed model & foreign key
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function getStartTimeFormattedAttribute()
    {
        return optional($this->start_time)->format('h:i A');
    }

    public function getEndTimeFormattedAttribute()
    {
        return optional($this->end_time)->format('h:i A');
    }
}
