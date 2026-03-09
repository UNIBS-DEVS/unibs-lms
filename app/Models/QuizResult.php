<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'total_marks',
        'obtained_marks',
        'percentage',
        'result',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /* ---------------- Relationships ---------------- */

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function learner()
    {
        return $this->belongsTo(User::class, 'learner_id');
    }
}
