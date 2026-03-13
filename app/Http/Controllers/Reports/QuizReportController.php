<?php

namespace App\Http\Controllers\Reports;

use App\Models\Quiz;
use App\Models\Batch;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use App\Mail\QuizReportMail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\QuizReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class QuizReportController extends Controller
{

    public function index(Request $request)
    {
        return view('reports.quiz.index', [
            'batches' => Batch::orderBy('name')->get(),
        ]);
    }

    /**
     * Base Query (Common filter)
     */
    private function filteredQuery(Request $request)
    {
        $query = QuizAttempt::query()
            ->whereIn('status', [
                'completed_auto',
                'pending_manual_review',
                'result_published',
            ]);

        if ($request->filled('batch_id')) {
            $query->whereHas('quiz', function ($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            });
        }

        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('started_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('started_at', '<=', $request->to_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    /**
     * Combine retakes -> keep best attempt
     */
    private function getFinalAttempts(Request $request)
    {
        return $this->filteredQuery($request)
            ->with([
                'quiz.batch.trainers',
                'quiz.questions',
                'quiz',
                'user'
            ])
            ->get()
            ->groupBy(function ($attempt) {
                return $attempt->quiz_id . '_' . $attempt->user_id;
            })
            ->map(function ($group) {

                // Choose BEST score attempt
                return $group->sortByDesc('score')->first();
            })
            ->values();
    }

    /**
     * AJAX Filter
     */
    public function filter(Request $request)
    {
        $request->validate([
            'batch_id' => 'required',
            'quiz_id' => 'required'
        ]);

        $attempts = $this->getFinalAttempts($request);

        $summary = [
            'total' => $attempts->count(),
            'completed' => $attempts->whereIn('status', [
                'completed_auto',
                'result_published'
            ])->count(),
            'pending_manual' => $attempts
                ->where('status', 'pending_manual_review')
                ->count(),
            'average_score' => round($attempts->avg('score') ?? 0, 2),
        ];

        $data = $attempts->map(function ($attempt) {

            $trainer = optional(
                optional($attempt->quiz?->batch)
                    ->trainers
                    ->first()
            )->name ?? '-';

            return [
                'trainer' => $trainer,
                'quiz' => $attempt->quiz->title ?? '-',
                'learner' => $attempt->user->name ?? '-',
                'status' => $attempt->status,
                'score' => $attempt->score ?? '-',
                'total' => $attempt->quiz?->totalMarks() ?? '-',
                'percentage' => $attempt->percentage() ?? 0,
                'result' => $attempt->isPassed() ? 'Pass' : 'Fail',
                'completed_at' => optional($attempt->completed_at)
                    ->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'data' => $data,
            'summary' => $summary,
        ]);
    }

    /**
     * Excel Export
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        $attempts = $this->getFinalAttempts($request);

        if ($attempts->isEmpty()) {
            return back()->with('error', 'No data available for export.');
        }

        $clientCode = session('client_code');
        $batchName = $attempts->first()?->quiz?->batch?->name ?? 'Batch';
        $quizName  = $attempts->first()?->quiz?->title ?? 'Quiz';

        if (!empty($user->email)) {

            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new QuizReportMail(
                    $attempts->toArray(),
                    $user,
                    $batchName,
                    $quizName,
                    'excel',
                    $clientCode
                )
            );
        }

        return Excel::download(
            new QuizReportExport($attempts),
            'Quiz Report-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    /**
     * PDF Export
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $attempts = $this->getFinalAttempts($request);

        if ($attempts->isEmpty()) {
            return back()->with('error', 'No data available.');
        }

        $clientCode = session('client_code');
        $batchName = $attempts->first()?->quiz?->batch?->name ?? 'Batch';
        $quizName  = $attempts->first()?->quiz?->title ?? 'Quiz';

        if (!empty($user->email)) {

            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new QuizReportMail(
                    $attempts->toArray(),
                    $user,
                    $batchName,
                    $quizName,
                    'pdf',
                    $clientCode
                )
            );
        }

        return Pdf::loadView('reports.quiz.pdf', [
            'attempts' => $attempts,
            'batchName' => $batchName,
            'quizName' => $quizName
        ])
            ->setPaper('a4', 'landscape')
            ->download('quiz-report-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Load quizzes by batch (AJAX)
     */
    public function getQuizzesByBatch($batchId)
    {
        return Quiz::where('batch_id', $batchId)
            ->orderBy('title')
            ->get(['id', 'title']);
    }
}
