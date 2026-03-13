<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchFbSummary;
use App\Models\BatchSession;
use App\Models\QuizAttempt;
use App\Models\QuizResult;
use Illuminate\Http\Request;

class PerformanceReportController extends Controller
{

    public function index()
    {
        $batches = Batch::orderBy('name')->get();

        return view('reports.performance.index', compact('batches'));
    }

    public function filter(Request $request)
    {
        $batch_id = $request->batch_id;

        if (!$batch_id) {
            return response()->json([
                'data' => [],
                'summary' => [
                    'attendance' => 0,
                    'quiz' => 0,
                    'feedback' => 0,
                    'avg_score' => 0
                ]
            ]);
        }

        $batch = Batch::with('learners')->findOrFail($batch_id);

        $sessions = BatchSession::where('batch_id', $batch_id)
            ->when($request->start_date, fn($q) =>
            $q->whereDate('start_date', '>=', $request->start_date))
            ->when($request->end_date, fn($q) =>
            $q->whereDate('end_date', '<=', $request->end_date))
            ->get();

        $data = [];

        foreach ($batch->learners as $learner) {

            /* ==================================================
               STEP 1 : ATTENDANCE SCORE
            ================================================== */

            $attendanceScore = $this->calculateAttendance($learner->id, $sessions, $batch);


            /* ==================================================
               STEP 2 : QUIZ SCORE
            ================================================== */

            $quizScore = $this->calculateQuiz($learner->id, $batch_id);




            /* ==================================================
               STEP 3 : FEEDBACK SCORE
            ================================================== */

            $feedbackScore = $this->calculateFeedback($learner->id, $batch_id);



            /* ==================================================
               STEP 4 : FINAL PERFORMANCE
            ================================================== */

            $finalScore =
                ($attendanceScore * $batch->attendance_percentage / 100) +
                ($quizScore * $batch->quiz_percentage / 100) +
                ($feedbackScore * $batch->feedback_percentage / 100);

            $finalScore = round($finalScore, 2);


            /* ==================================================
               STATUS
            ================================================== */

            if ($finalScore >= $batch->green_percentage) {
                $status = "Green";
            } elseif ($finalScore >= $batch->amber_percentage) {
                $status = "Amber";
            } else {
                $status = "Red";
            }


            $data[] = [
                'learner_name' => $learner->name,
                'attendance' => $attendanceScore,
                'quiz' => $quizScore,
                'feedback' => $feedbackScore,
                'avg_score' => $finalScore,
                'status' => $status
            ];
        }


        /* ==================================================
           SUMMARY
        ================================================== */

        $count = count($data);

        $summary = [
            'attendance' => $count ? round(array_sum(array_column($data, 'attendance')) / $count, 2) : 0,
            'quiz' => $count ? round(array_sum(array_column($data, 'quiz')) / $count, 2) : 0,
            'feedback' => $count ? round(array_sum(array_column($data, 'feedback')) / $count, 2) : 0,
            'avg_score' => $count ? round(array_sum(array_column($data, 'avg_score')) / $count, 2) : 0,
        ];

        return response()->json([
            'data' => $data,
            'summary' => $summary
        ]);
    }


    /* ==================================================
       ATTENDANCE FUNCTION
    ================================================== */

    private function calculateAttendance($learner_id, $sessions, $batch)
    {
        $totalScore = 0;
        $totalSessions = $sessions->count();

        foreach ($sessions as $session) {

            $attendance = $session->attendances()
                ->where('learner_id', $learner_id)
                ->first();

            if (!$attendance) continue;

            $score = 0;

            if ($attendance->isPresent()) {
                $score += $batch->present_value;
            }

            if ($attendance->isLate()) {
                $score -= $batch->late_entry_value;
            }

            if ($attendance->isEarlyExit()) {
                $score -= $batch->early_exit_value;
            }

            $totalScore += $score;
        }

        return $totalSessions
            ? round($totalScore / $totalSessions, 2)
            : 0;
    }


    /* ==================================================
       QUIZ FUNCTION (BEST ATTEMPT)
    ================================================== */
    private function calculateQuiz($learner_id, $batch_id)
    {
        $results = QuizResult::where('learner_id', $learner_id)
            ->whereHas('attempt', function ($q) use ($batch_id) {
                $q->where('batch_id', $batch_id);
            })
            ->get();

        if ($results->isEmpty()) {
            return 0;
        }

        $bestScores = $results
            ->groupBy('quiz_attempt_id')
            ->map(fn($group) => $group->max('percentage'));

        return round($bestScores->avg(), 2);
    }
    /* ==================================================
       FEEDBACK FUNCTION
    ================================================== */

    private function calculateFeedback($learner_id, $batch_id)
    {
        $feedbacks = BatchFbSummary::where('batch_id', $batch_id)
            ->where('submitted_by', $learner_id)
            ->pluck('avg_score');

        if ($feedbacks->isEmpty()) {
            return 0;
        }

        return round($feedbacks->avg(), 2);
    }
}
