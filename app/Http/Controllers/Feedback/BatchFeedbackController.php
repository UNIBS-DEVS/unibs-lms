<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchFbSummary;
use App\Models\BatchFbSubmissionDetail;
use App\Models\BatchFeedbackQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchFeedbackController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'learner') {
            $batches = $user->batches;
        } else {
            $batches = Batch::all();
        }

        return view('batch-feedback.index', compact('batches'));
    }

    public function store(Request $request)
    {

        // dd($request);

        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'scores'   => 'required|array',
            'remarks'  => 'nullable|string',
        ]);

        $user = Auth::user();

        if ($user->role === 'learner') {

            $request->validate([
                'trainer_id' => 'required|exists:users,id',
            ]);

            $type        = 'learner';
            $trainerId   = $request->trainer_id;
            $submittedBy = $user->id;
        } else {

            $request->validate([
                'feedback_type' => 'required|in:learner,trainer',
            ]);

            $type        = $request->feedback_type;
            $trainerId   = $user->id;
            $submittedBy = $user->id;
        }

        $avgScore = collect($request->scores)->avg();

        $summary = BatchFbSummary::create([
            'batch_id'     => $request->batch_id,
            'trainer_id'   => $trainerId,
            'type'         => $type,
            'submitted_by' => $submittedBy,
            'avg_score'    => round($avgScore, 2),
            'remarks'      => $request->remarks,
        ]);

        // 🔥 STORE QUESTION TEXT
        foreach ($request->scores as $questionId => $score) {

            $question = BatchFeedbackQuestion::find($questionId);

            BatchFbSubmissionDetail::create([
                'summery_id' => $summary->id,
                'question'   => $question->question,   // store TEXT
                'score'      => $score,
            ]);
        }

        return back()->with('success', 'Feedback submitted successfully.');
    }

    public function questions($type)
    {
        return response()->json(
            BatchFeedbackQuestion::where('type', $type)->get()
        );
    }

    public function trainers(Batch $batch)
    {
        return response()->json(
            $batch->trainers()->select('users.id', 'users.name')->get()
        );
    }

    public function learners(Batch $batch)
    {
        return response()->json(
            $batch->learners()->select('users.id', 'users.name')->get()
        );
    }

    public function previous(Request $request)
    {
        $summary = BatchFbSummary::where('batch_id', $request->batch_id)
            ->where('type', $request->type)
            ->where('submitted_by', Auth::id())
            ->with('details')
            ->first();

        if (!$summary) {
            return response()->json(null);
        }

        // Convert question text → array
        $scores = [];
        foreach ($summary->details as $detail) {
            $scores[$detail->question] = $detail->score;
        }

        return response()->json([
            'remarks' => $summary->remarks,
            'scores'  => $scores,
        ]);
    }
}
