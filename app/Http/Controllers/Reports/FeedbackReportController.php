<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Mail\FeedbackReportMail;
use App\Models\Batch;
use App\Models\BatchFbSummary;
use App\Models\DefaultFeedback;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FeedbackReportExport;

class FeedbackReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $batches = $this->getUserBatches($user);

        $summaries = $this->filteredQuery($request)->get();

        $summaryStats = [
            'total' => $summaries->count(),
            'trainer' => $summaries->where('type', 'trainer')->count(),
            'learner' => $summaries->where('type', 'learner')->count(),
            'average_score' => round($summaries->avg('avg_score') ?? 0, 2),
        ];

        // dd($request->all());
        return view('reports.feedback.index', [
            'batches' => $batches,
            'summaries' => $summaries,
            'summary' => $summaryStats,
        ]);
    }

    /**
     * Base Filter Query
     */
    private function filteredQuery(Request $request)
    {
        $query = BatchFbSummary::query()
            ->with([
                'batch:id,name',
                'trainer:id,name',
                'learner:id,name',
                'details'
            ]);

        if ($request->filled('batch_id')) {
            $query->where('batch_id', (int) $request->batch_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return $query->latest('created_at');
    }

    public function filter(Request $request)
    {

        dd('jhug');

        $query = BatchFbSummary::with([
            'batch',
            'trainer',
            'learner',
            'details'
        ]);

        if ($request->batch_id) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $summaries = $query->latest()->get();


        $data = $summaries->map(function ($s) {

            $categories = $s->details
                ->pluck('category')
                ->filter()
                ->unique()
                ->values();

            return [

                'learner' => $s->learner->name ?? '-',

                'trainer' => $s->trainer->name ?? '-',

                'categories' => $categories,

                'avg_score' => number_format($s->avg_score, 1),

                'date' => optional($s->created_at)->format('d M Y'),

            ];
        });


        return response()->json([

            'data' => $data,

            'summary' => [

                'total' => $summaries->count(),

                'trainer' => $summaries->where('type', 'trainer')->count(),

                'learner' => $summaries->where('type', 'learner')->count(),

                'avg_score' => round($summaries->avg('avg_score'), 2)

            ]

        ]);
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

        return Batch::where('customer_id', $user->id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        $summaries = $this->filteredQuery($request)->get();

        if ($summaries->isEmpty()) {
            return back()->with('error', 'No data available for export.');
        }

        $batchName = $summaries->first()?->batch?->name ?? 'Batch';
        $feedbackType = $request->type;

        $clientCode = session('client_code');

        if (!empty($user->email)) {
            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new FeedbackReportMail(
                    $summaries->toArray(),
                    $user,
                    'excel',
                    $clientCode,
                    $batchName,
                    $feedbackType
                )
            );
        }

        return Excel::download(
            new FeedbackReportExport($summaries),
            'feedback-report-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $summaries = $this->filteredQuery($request)->get();

        if ($summaries->isEmpty()) {
            return back()->with('error', 'No data available.');
        }

        $batchName = $summaries->first()?->batch?->name ?? 'Batch';
        $feedbackType = $request->type;

        $clientCode = session('client_code');

        if (!empty($user->email)) {
            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new FeedbackReportMail(
                    $summaries->toArray(),
                    $user,
                    'pdf',
                    $clientCode,
                    $batchName,
                    $feedbackType
                )
            );
        }

        return Pdf::loadView('reports.feedback.pdf', [
            'summaries' => $summaries,
            'batchName' => $batchName,
            'feedbackType' => $feedbackType
        ])
            ->setPaper('a4', 'landscape')
            ->download('feedback-report-' . now()->format('Ymd-His') . '.pdf');
    }
}
