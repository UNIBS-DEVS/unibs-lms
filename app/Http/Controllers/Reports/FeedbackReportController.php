<?php

namespace App\Http\Controllers\Reports;

use App\Exports\FeedbackReportExport;
use App\Http\Controllers\Controller;
use App\Mail\FeedbackReportMail;
use App\Models\Batch;
use App\Models\BatchFbSubmissionDetail;
use App\Models\BatchFbSummary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class FeedbackReportController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        $batches = $this->getUserBatches($user);

        // dd($user);
        return view('reports.feedback.index', compact('batches'));
    }

    /**
     * Base Query for all filters
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
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // dd($query);
        return $query->latest('created_at');
    }

    /**
     * AJAX Filter
     */
    public function filter(Request $request)
    {

        $summaries = $this->filteredQuery($request)->get();

        $data = $summaries->map(function ($s) {

            $categories = $s->details
                ->pluck('category')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            return [
                'id' => $s->id,
                'learner' => $s->learner->name ?? '-',
                'trainer' => $s->trainer->name ?? '-',
                'categories' => $categories,
                'avg_score' => number_format($s->avg_score ?? 0, 1),
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

    /**
     * Get User Batches
     */
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
        // dd($clientCode);

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

    public function details($id)
    {
        $summary = BatchFbSummary::with('details')->findOrFail($id);

        $details = $summary->details->map(function ($d) {
            return [
                'question' => $d->question,
                'score' => $d->score
            ];
        });

        return response()->json($details);
    }
}
