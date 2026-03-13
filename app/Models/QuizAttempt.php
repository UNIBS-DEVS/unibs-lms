<?php

namespace App\Models;

use App\Models\QuizAttemptAnswer;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
        'batch_id',
        'batch_session_id',
        'score',
        'status',
        'started_at',
        'completed_at',
        'time_limit_minutes',        // ✅ REQUIRED
        'question_order',
    ];

    protected $casts = [
        'started_at'     => 'datetime',
        'ends_at'    => 'datetime',
        'completed_at'   => 'datetime',
        'question_order' => 'array',
    ];

    /* ---------------- Relationships ---------------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function session()
    {
        return $this->belongsTo(BatchSession::class, 'batch_session_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }


    public function result()
    {
        return $this->hasOne(QuizResult::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function markCompletedAuto()
    {
        $this->update([
            'status' => 'completed_auto',
            'completed_at' => now(),
        ]);
    }

    public function markPendingManual()
    {
        $this->update([
            'status' => 'pending_manual_review',
            'completed_at' => now(),
        ]);
    }

    /* ---------------- Status Helpers ---------------- */

    public function markInProgress()
    {
        $this->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Auto-complete quiz (NO manual questions)
     */
    public function finalizeScore()
    {
        $score = $this->answers()->sum('marks_obtained');

        $this->update([
            'score' => $score,
            'status' => 'completed_auto',
            'submitted_at' => now()
        ]);

        $totalMarks = $this->quiz->totalMarks();

        $percentage = $totalMarks > 0
            ? ($score / $totalMarks) * 100
            : 0;

        QuizResult::updateOrCreate(
            ['quiz_attempt_id' => $this->id],
            [
                'learner_id' => $this->user_id,
                'total_marks' => $totalMarks,
                'obtained_marks' => $score,
                'percentage' => round($percentage, 2),
                'result' => $percentage >= $this->quiz->minimum_passing_percentage ? 'pass' : 'fail',
                'published_at' => now()
            ]
        );
    }
    /**
     * Publish final result (AFTER manual review)
     */
    public function publishResult()
    {
        $total = $this->answers()
            ->whereNotNull('marks_obtained')
            ->sum('marks_obtained');

        $this->update([
            'score'  => max(0, $total),
            'status' => 'result_published',
        ]);

        $totalMarks = $this->quiz->totalMarks();
        $percentage = $totalMarks ? ($total / $totalMarks) * 100 : 0;

        QuizResult::updateOrCreate(
            ['quiz_attempt_id' => $this->id],
            [
                'learner_id'     => $this->user_id,
                'total_marks'    => $totalMarks,
                'obtained_marks' => $total,
                'percentage'     => round($percentage, 2),
                'result'         => $percentage >= $this->quiz->minimum_passing_percentage ? 'pass' : 'fail',
                'published_at'   => now()
            ]
        );
    }

    /* ---------------- Attempt Guards ---------------- */

    public static function activeAttempt(int $userId, int $quizId): ?self
    {
        return self::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->where('status', 'in_progress')
            ->first();
    }

    public static function hasCompletedAttempt(int $userId, int $quizId): bool
    {
        return self::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->whereIn('status', [
                'completed_auto',
                'pending_manual_review',
                'result_published',
            ])
            ->exists();
    }

    public function isFinalized(): bool
    {
        return in_array($this->status, [
            'completed_auto',
            'pending_manual_review',
            'result_published',
        ]);
    }

    public function getEndsAtAttribute()
    {
        if (!$this->started_at || !$this->quiz?->time_limit_minutes) {
            return null;
        }

        return \Carbon\Carbon::parse($this->started_at)
            ->addMinutes($this->quiz->time_limit_minutes);
    }

    public function shouldShowResultImmediately(): bool
    {
        // Manual questions → never show immediately
        $hasManual = $this->quiz->questions()
            ->whereIn('question_type', ['text', 'file'])
            ->exists();

        if ($hasManual) {
            return false;
        }

        return (bool) $this->quiz->show_results_immediately;
    }

    public static function attemptCount(int $userId, int $quizId): int
    {
        return self::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->count();
    }

    public static function canRetake(int $userId, Quiz $quiz): bool
    {
        if (!$quiz->max_attempts) {
            return true; // unlimited
        }

        $used = self::attemptCount($userId, $quiz->id);

        return $used < $quiz->max_attempts;
    }

    public function unansweredCount(): int
    {
        $totalQuestions = count($this->question_order ?? []);

        $answered = $this->answers()
            ->where(function ($q) {
                $q->whereNotNull('answer_text')
                    ->orWhereNotNull('answer_file')
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('answer_options')
                            ->whereRaw('JSON_LENGTH(answer_options) > 0');
                    });
            })
            ->count();

        return max(0, $totalQuestions - $answered);
    }

    public function attemptSummary(): array
    {
        $total = count($this->question_order ?? []);

        $answered = $this->answers()
            ->where(function ($q) {
                $q->whereNotNull('answer_text')
                    ->orWhereNotNull('answer_file')
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('answer_options')
                            ->whereRaw('JSON_LENGTH(answer_options) > 0');
                    });
            })
            ->count();

        $correct = $this->answers()
            ->where('is_correct', true)
            ->count();

        $wrong = $this->answers()
            ->where('is_correct', false)
            ->count();

        $skipped = max(0, $total - $answered);

        return compact('total', 'answered', 'skipped', 'correct', 'wrong');
    }

    public function percentage(): ?float
    {
        if ($this->score === null) {
            return null;
        }

        $totalMarks = $this->quiz?->totalMarks();

        if (!$totalMarks || $totalMarks <= 0) {
            return null;
        }

        return round(($this->score / $totalMarks) * 100, 2);
    }

    public function isPassed(): ?bool
    {
        $percent = $this->percentage();
        $pass = $this->quiz->minimum_passing_percentage;

        if ($percent === null || $pass === null) {
            return null;
        }

        return $percent >= $pass;
    }
}
