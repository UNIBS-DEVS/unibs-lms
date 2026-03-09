<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttemptAnswer extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'answer_text',
        'answer_options',
        'answer_file',
        'is_correct',
        'marks_obtained',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'answer_options' => 'array',
        'is_correct' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /* ---------------- Relationships ---------------- */

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /* ---------------- Evaluation Helpers ---------------- */

    public function autoEvaluate(bool $isCorrect, float $marks)
    {
        $this->update([
            'is_correct' => $isCorrect,
            'marks_obtained' => $marks,
        ]);
    }

    public function manualReview(float $marks, int $trainerId)
    {
        $this->update([
            'marks_obtained' => $marks,
            'is_correct' => null, // optional
            'reviewed_by' => $trainerId,
            'reviewed_at' => now(),
        ]);
    }
}
