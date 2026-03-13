<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'name',
        'status',
        'customer_id',
        'start_date',
        'end_date',

        'attendance_percentage',
        'quiz_percentage',
        'feedback_percentage',

        'red_percentage',
        'amber_percentage',
        'green_percentage',

        'present_value',
        'late_entry_value',
        'early_exit_value',
    ];

    protected $casts = [
        'late_entry_value'  => 'float',
        'early_exit_value'  => 'float',
    ];

    /** ONLY learners via pivot */
    public function learners()
    {
        return $this->belongsToMany(
            User::class,
            'batch_learners',
            'batch_id',
            'learner_id',
        )->where('role', 'learner');
    }

    // public function learners()
    // {
    //     return $this->belongsToMany(User::class, 'batch_user', 'batch_id', 'learner_id');
    // }

    /** ONLY Trainer via pivot */
    // public function trainers()
    // {
    //     return $this->belongsToMany(
    //         User::class,
    //         'batch_trainers',
    //         'batch_id',
    //         'trainer_id'
    //     )->where('role', 'trainer');
    // }

    public function trainers()
    {
        return $this->belongsToMany(
            User::class,
            'batch_trainers',
            'batch_id',
            'trainer_id'
        );
    }


    /** ONLY Courses via pivot*/
    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'batch_courses',
            'batch_id',
            'course_id'
        );
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function tocs()
    {
        return $this->hasMany(BatchToc::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function feedbackQuestions()
    {
        return $this->hasMany(BatchFeedbackQuestion::class);
    }
}
