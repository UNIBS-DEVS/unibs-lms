<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\BatchSession;
use App\Models\BatchFbSummary;
use App\Models\QuizResult;

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
                'summary' => ['attendance' => 0, 'quiz' => 0, 'feedback' => 0]
            ]);
        }

        $batch = Batch::with('learners')->findOrFail($batch_id);
        $data = []; 

        foreach ($batch->learners as $learner) {

            /** ----------------
             * ATTENDANCE AVERAGE
             * ---------------- */
            $sessions = BatchSession::where('batch_id', $batch_id)
                ->when($request->start_date, fn($q) => $q->whereDate('start_date', '>=', $request->start_date))
                ->when($request->end_date, fn($q) => $q->whereDate('end_date', '<=', $request->end_date))
                ->get();

            $totalScore = 0;
            $totalSessions = $sessions->count();

            foreach ($sessions as $session) {
                $attendance = $session->attendances()
                    ->where('learner_id', $learner->id)
                    ->first();

                if ($attendance) {
                    $presentValue   = $batch->present_value ?? 1;
                    $lateValue      = $batch->late_entry_value ?? 0;
                    $earlyExitValue = $batch->early_exit_value ?? 0;

                    $score = 0;
                    $score += $attendance->isPresent() ? $presentValue : 0;
                    $score += $attendance->isLate() ? $lateValue : 0;
                    $score += $attendance->isEarlyExit() ? $earlyExitValue : 0;

                    $totalScore += $score;
                }
            }

            $avgAttendance = $totalSessions ? round($totalScore / $totalSessions, 2) : 0;

            /** ----------------
             * QUIZ AVERAGE
             * ---------------- */
            $quizResults = QuizResult::where('learner_id', $learner->id)
                ->whereHas('attempt', function ($q) use ($batch_id) {
                    $q->whereHas('session', function ($s) use ($batch_id) {
                        $s->where('batch_id', $batch_id);
                    });
                })->get();

            $avgQuiz = $quizResults->count()
                ? round($quizResults->avg('percentage'), 2)
                : 0;

            /** ----------------
             * FEEDBACK AVERAGE
             * ---------------- */
            $feedbackSummary = BatchFbSummary::where('batch_id', $batch_id)
                ->where('submitted_by', $learner->id)
                ->first();

            $avgFeedback = $feedbackSummary ? round($feedbackSummary->avg_score, 2) : 0;

            /** ----------------
             * PUSH DATA
             * ---------------- */
            $avgScore = round(($avgAttendance + $avgQuiz + $avgFeedback) / 3, 2);
            $status = $avgScore >= 75 ? 'Green' : ($avgScore >= 50 ? 'Amber' : 'Red');

            $data[] = [
                'learner_name' => $learner->name,
                'attendance'   => $avgAttendance,
                'quiz'         => $avgQuiz,
                'feedback'     => $avgFeedback,
                'avg_score'    => $avgScore,
                'status'       => $status,
            ];
        }

        /** ----------------
         * BATCH SUMMARY
         * ---------------- */
        $summary = [
            'attendance' => $data ? round(array_sum(array_column($data, 'attendance')) / count($data), 2) : 0,
            'quiz'       => $data ? round(array_sum(array_column($data, 'quiz')) / count($data), 2) : 0,
            'feedback'   => $data ? round(array_sum(array_column($data, 'feedback')) / count($data), 2) : 0,
            'avg_score'  => $data ? round(array_sum(array_column($data, 'avg_score')) / count($data), 2) : 0,
        ];

        return response()->json([
            'data' => $data,
            'summary' => $summary
        ]);
    }
}
