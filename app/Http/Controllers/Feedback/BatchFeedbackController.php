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
            $submittedBy = $request->learner_id;
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

        $questions = BatchFeedbackQuestion::whereIn('id', array_keys($scores))
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

    public function questions(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'learner') {
            $type = 'trainer';
        } else {
            $type = 'learner';
        }

        return response()->json(
            BatchFeedbackQuestion::select('id', 'question', 'category')
                ->where('type', $type)
                ->where('batch_id', $request->batch_id) // ✅ IMPORTANT
                ->get()
        );
    }

    public function trainers($batchId)
    {
        $user = auth()->user();

        $batch = Batch::with([
            'trainers' => function ($q) {
                $q->select('users.id', 'users.name');
            }
        ])->findOrFail($batchId);

        // 🔐 Restrict learner → only his batches
        if ($user->role === 'learner') {
            $isAllowed = $batch->learners()
                ->where('learner_id', $user->id)
                ->exists();

            if (!$isAllowed) {
                return response()->json([], 403);
            }
        }

        return response()->json($batch->trainers);
    }

    public function learners(Batch $batch)
    {
        return response()->json(
            $batch->learners()->select('users.id', 'users.name')->get()
        );
    }

    public function getTrainersByBatch($batchId)
    {
        $batch = Batch::with('trainers:id,name')->findOrFail($batchId);

        return response()->json($batch->trainers);
    }
}
