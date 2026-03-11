<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchFbSubmissionDetail;
use App\Models\BatchFbSummary;
use App\Models\BatchFeedbackQuestion;
use App\Models\DefaultFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\Environment\Console;

class BatchFeedbackController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'learner') {
            $batches = $user->learnerBatches()->select('batches.id', 'batches.name')->get();
        } elseif ($user->role === 'trainer') {
            $batches = $user->trainerBatches()->select('batches.id', 'batches.name')->get();
        } else {
            $batches = Batch::select('id', 'name')->get();
        }

        // dd($batches);

        return view('batch-feedback.index', compact('batches'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'scores.*' => 'nullable|numeric|min:1|max:5',
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
                'learner_id' => 'required|exists:users,id',
            ]);

            $type        = 'trainer';
            $trainerId   = $user->id;
            $submittedBy = $user->id;
        }

        $scores = $request->input('scores', []);

        if (empty($scores)) {
            return back()->with('error', 'Please rate at least one question.');
        }

        $avgScore = collect($scores)->filter()->avg();

        $summary = BatchFbSummary::create([
            'batch_id'     => $request->batch_id,
            'trainer_id'   => $trainerId,
            'type'         => $type,
            'submitted_by' => $submittedBy,
            'avg_score'    => round($avgScore, 2),
            'remarks'      => $request->remarks,
        ]);

        /*
    Fetch questions from default_feedbacks
    */

        $questions = DefaultFeedback::whereIn('id', array_keys($scores))
            ->get()
            ->keyBy('id');

        foreach ($scores as $questionId => $score) {

            if (!$score || !isset($questions[$questionId])) {
                continue;
            }

            $question = $questions[$questionId];

            BatchFbSubmissionDetail::create([
                'summary_id' => $summary->id,
                'category'   => $question->category,   // ✅ from default_feedbacks
                'question'   => $question->question,
                'score'      => $score,
            ]);
        }

        return back()->with('success', 'Feedback submitted successfully');
    }

    /*
    LOAD QUESTIONS BASED ON ROLE
    */

    public function questions()
    {
        $user = Auth::user();

        // If learner gives feedback → show trainer questions
        if ($user->role === 'learner') {
            $type = 'trainer';
        }
        // If trainer/admin gives feedback → show learner questions
        else {
            $type = 'learner';
        }

        return response()->json(
            BatchFeedbackQuestion::select('id', 'question', 'category')
                ->where('type', $type)
                ->get()
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

    // public function previous(Request $request)
    // {
    //     $summary = BatchFbSummary::where('batch_id', $request->batch_id)
    //         ->where('type', $request->type)
    //         ->where('submitted_by', Auth::id())
    //         ->with('details')
    //         ->first();

    //     if (!$summary) {
    //         return response()->json(null);
    //     }

    //     // Convert question text → array
    //     $scores = [];
    //     foreach ($summary->details as $detail) {
    //         $scores[$detail->question] = $detail->score;
    //     }

    //     return response()->json([
    //         'remarks' => $summary->remarks,
    //         'scores'  => $scores,
    //     ]);
    // }
}
