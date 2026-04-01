<?php

namespace App\Http\Controllers\Reports;

use App\Exports\PerformanceReportExport;
use App\Http\Controllers\Controller;
use App\Mail\AttendanceReportMail;
use App\Mail\PerformanceReportMail;
use App\Models\Batch;
use App\Models\BatchFbSummary;
use App\Models\BatchSession;
use App\Models\QuizAttempt;
use App\Models\QuizResult;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PerformanceReportController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        $batches = $this->getUserBatches($user);

        return view('reports.performance.index', compact('batches'));
    }

    public function filter(Request $request)
    {
        if (!$request->batch_id) {
            return response()->json([
                'data' => [],
                'summary' => []
            ]);
        }

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

        $user = Auth::user();

        $batch = Batch::with('learners')
            ->where('id', $batch_id)

            ->when($user->role === 'trainer', function ($q) use ($user) {
                $q->whereHas(
                    'trainers',
                    fn($t) =>
                    $t->where('trainer_id', $user->id)
                );
            })

            ->when($user->role === 'learner', function ($q) use ($user) {
                $q->whereHas(
                    'learners',
                    fn($l) =>
                    $l->where('learner_id', $user->id)
                );
            })

            ->firstOrFail();

        $sessions = BatchSession::where('batch_id', $batch_id)
            ->when($request->start_date, fn($q) =>
            $q->whereDate('start_date', '>=', $request->start_date))
            ->when($request->end_date, fn($q) =>
            $q->whereDate('end_date', '<=', $request->end_date))
            ->get();

        $data = [];

        $learners = $batch->learners;

        if ($user->role === 'learner') {
            $learners = $learners->where('id', $user->id);
        }

        foreach ($learners as $learner) {

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

            /* convert attendance & feedback to percentage */
            $attendancePercent = ($attendanceScore / 5) * 100;
            $feedbackPercent   = ($feedbackScore / 5) * 100;

            $finalScore =
                ($attendancePercent * $batch->attendance_percentage / 100) +
                ($quizScore * $batch->quiz_percentage / 100) +
                ($feedbackPercent * $batch->feedback_percentage / 100);

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

            $attendancePercent = round(($attendanceScore / 5) * 100, 2);
            $feedbackPercent   = round(($feedbackScore / 5) * 100, 2);

            $data[] = [
                'learner_name' => $learner->name,
                'attendance' => $attendancePercent,
                'quiz' => round($quizScore, 2),
                'feedback' => $feedbackPercent,
                'avg_score' => round($finalScore, 2),
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

    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        $learners = User::where('role', 'learner')
            ->whereHas('learnerBatches', function ($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            })
            ->get();

        $data = $learners->map(function ($learner) {

            $attendance = 100;
            $quiz = 100;
            $feedback = 100;

            $avg = ($attendance + $quiz + $feedback) / 3;

            return [
                'learner' => $learner->name,
                'attendance' => $attendance,
                'quiz' => $quiz,
                'feedback' => $feedback,
                'avg_score' => $avg,
                'status' => $avg >= 80 ? 'Green' : 'Red'
            ];
        });

        /* Batch Name */
        $batchName = null;

        if ($request->filled('batch_id')) {
            $batch = Batch::find($request->batch_id);
            $batchName = $batch?->name;
        }

        /* Date Range */
        $startDate = $request->filled('start_date') ? $request->start_date : '-';
        $endDate   = $request->filled('end_date') ? $request->end_date : '-';

        /* Tenant Client Code */
        $clientCode = session('client_code');

        /* Send Email */
        if (!empty($user->email)) {

            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new PerformanceReportMail(
                    $data->toArray(),
                    $user,
                    'excel',
                    $batchName,
                    $startDate,
                    $endDate,
                    $clientCode
                )
            );
        }

        /* Download Excel */
        return Excel::download(
            new PerformanceReportExport($data, $batchName, $startDate, $endDate),
            'Performance Report-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $learners = User::where('role', 'learner')
            ->whereHas('learnerBatches', function ($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            })
            ->get();

        $data = $learners->map(function ($learner) {

            $attendance = 100;
            $quiz = 100;
            $feedback = 100;

            $avg = ($attendance + $quiz + $feedback) / 3;

            return [
                'learner' => $learner->name,
                'attendance' => $attendance,
                'quiz' => $quiz,
                'feedback' => $feedback,
                'avg_score' => $avg,
                'status' => $avg >= 80 ? 'Green' : 'Red'
            ];
        });

        /* Batch Name */
        $batchName = null;

        if ($request->filled('batch_id')) {
            $batch = Batch::find($request->batch_id);
            $batchName = $batch?->name;
        }

        /* Date Range */
        $startDate = $request->filled('start_date') ? $request->start_date : '-';
        $endDate   = $request->filled('end_date') ? $request->end_date : '-';

        /* Tenant Client Code */
        $clientCode = session('client_code');

        /* Send Email */
        if (!empty($user->email)) {

            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new PerformanceReportMail(
                    $data->toArray(),
                    $user,
                    'pdf',
                    $batchName,
                    $startDate,
                    $endDate,
                    $clientCode
                )
            );
        }

        /* Download PDF */
        return Pdf::loadView('reports.performance.pdf', [
            'performances' => $data,
            'batchName' => $batchName,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])
            ->setPaper('a4', 'landscape')
            ->download('Performance Report-' . now()->format('Ymd-His') . '.pdf');
    }

    private function getUserBatches($user)
    {
        if ($user->role === 'admin') {
            return Batch::orderBy('name')->get();
        }

        if ($user->role === 'trainer') {
            return Batch::whereHas('trainers', function ($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })->orderBy('name')->get();
        }

        if ($user->role === 'learner') {
            return Batch::whereHas('learners', function ($q) use ($user) {
                $q->where('learner_id', $user->id);
            })->orderBy('name')->get();
        }

        return collect();
    }
}
