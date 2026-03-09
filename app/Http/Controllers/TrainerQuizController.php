<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use App\Models\QuizAttemptAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TrainerQuizController extends Controller
{
    /**
     * List attempts waiting for manual review
     */
    public function index()
    {
        $attempts = QuizAttempt::where('status', 'pending_manual_review')
            ->with([
                'user',
                'quiz',
                'answers.question'
            ])
            ->get();

        return view('trainer.quiz_reviews.index', compact('attempts'));
    }

    /**
     * Show manual questions for an attempt
     */
    public function show(QuizAttempt $attempt)
    {
        // abort_if($attempt->status !== 'pending_manual_review', 403);
        if ($attempt->status !== 'pending_manual_review') {
            abort(403, 'This quiz is not pending manual review.');
        }

        $answers = $attempt->answers()
            ->with('question')
            ->whereHas(
                'question',
                fn($q) =>
                $q->where('marking_type', 'manual')
            )
            ->get();

        return view('trainer.quiz_reviews.show', compact('attempt', 'answers'));
    }

    /**
     * Review & mark a single answer
     */
    // public function reviewAnswer(Request $request, QuizAttemptAnswer $answer)
    // {
    //     $request->validate([
    //         'marks_obtained' => [
    //             'required',
    //             'numeric',
    //             'min:0',
    //             'max:' . $answer->question->max_marks,
    //         ],
    //         'is_correct' => 'required|boolean',
    //     ]);

    //     $answer->update([
    //         'is_correct'     => $request->is_correct,
    //         'marks_obtained' => $request->marks_obtained,
    //         'reviewed_by'    => Auth::id(),
    //         'reviewed_at'    => now(),
    //     ]);

    //     return back()->with('success', 'Answer reviewed successfully');
    // }

    public function reviewAnswer(Request $request, QuizAttemptAnswer $answer)
    {
        $request->validate([
            'marks_obtained' => [
                'required',
                'numeric',
                'min:0',
                'max:' . $answer->question->max_marks,
            ],
            'is_correct' => 'required|boolean',
        ]);

        $answer->update([
            'is_correct'     => $request->is_correct,
            'marks_obtained' => $request->marks_obtained,
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
        ]);

        return response()->json([
            'status' => 'success'
        ]);
    }


    // origenal
    public function publish(QuizAttempt $attempt)
    {
        if ($attempt->status !== 'pending_manual_review') {
            return back()->with('error', 'Quiz cannot be published.');
        }

        // Safety check: ensure no manual answers are unreviewed
        $pending = $attempt->answers()
            ->whereHas('question', function ($q) {
                $q->where('marking_type', 'manual');
            })
            ->whereNull('marks_obtained')
            ->exists();

        if ($pending) {
            return back()->with('error', 'Please review all manual answers first.');
        }

        // ✅ Finalize score
        $attempt->publishResult();

        return redirect()
            ->route('trainer.quiz-reviews.index')
            ->with('success', 'Quiz result published successfully.');
    }

    public function viewFile(QuizAttemptAnswer $answer)
    {
        $answer->load('quizAttempt');

        abort_if(!$answer->quizAttempt, 404);
        abort_if(!Auth::check(), 403);

        $user = Auth::user();

        // Trainer/Admin → all
        if (!in_array($user->role, ['trainer', 'admin'])) {
            // Learner → own attempt only
            abort_if($answer->quizAttempt->user_id !== $user->id, 403);
        }

        abort_if(!$answer->answer_file, 404);

        $path = storage_path('app/public/' . $answer->answer_file);

        abort_if(!file_exists($path), 404);

        $mimeType = File::mimeType($path);

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }


    // public function publish(QuizAttempt $attempt)
    // {
    //     if ($attempt->status !== 'pending_manual_review') {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Quiz cannot be published.'
    //         ], 422);
    //     }

    //     $pending = $attempt->answers()
    //         ->whereHas('question', function ($q) {
    //             $q->where('marking_type', 'manual');
    //         })
    //         ->whereNull('marks_obtained')
    //         ->exists();

    //     if ($pending) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Please review all manual answers first.'
    //         ], 422);
    //     }

    //     $attempt->publishResult();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Quiz result published successfully.'
    //     ]);
    // }
}
