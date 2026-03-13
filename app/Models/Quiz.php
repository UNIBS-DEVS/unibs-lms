<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question; // ✅ IMPORTANT

class Quiz extends Model
{
    protected $fillable = [
        'batch_id',
        'title',
        'quiz_type',
        'minimum_passing_percentage',
        'time_limit_minutes',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_results_immediately',
        'question_per_page',
        'visible_start_date',
        'visible_start_time',
        'visible_end_date',
        'visible_end_time',
        'difficulty_level',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'minimum_passing_percentage' => 'float',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function questions()
    {
        return $this->belongsToMany(
            Question::class,
            'quiz_question',
            'quiz_id',
            'question_id'
        );
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function totalMarks(): int
    {
        return (int) $this->questions()->sum('max_marks');
    }

    public function session()
    {
        return $this->belongsTo(BatchSession::class, 'batch_session_id');
    }
}
