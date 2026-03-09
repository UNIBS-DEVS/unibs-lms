<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'course_id',
        'topic_id',
        'question_type',
        'question_text',
        'max_marks',
        'negative_marks',
        'marking_type',
        'is_active',
        'question_file_path',
        'allowed_file_types',
        'max_file_size_mb',
    ];

    protected $casts = [
        'allowed_file_types' => 'array',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function topic()
    {
        return $this->belongsTo(CourseTopic::class, 'topic_id');
    }
    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function fileSettings()
    {
        return $this->hasOne(FileQuestionSetting::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(
            Quiz::class,
            'quiz_question'   // 👈 pivot table name
        );
    }
}
