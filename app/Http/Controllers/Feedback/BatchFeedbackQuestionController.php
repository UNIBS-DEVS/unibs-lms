<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchFeedbackQuestion;
use App\Models\DefaultFeedback; // existing table
use Illuminate\Http\Request;

class BatchFeedbackQuestionController extends Controller
{
    public function index(Batch $batch)
    {
        $questions = $batch->feedbackQuestions()
            ->latest()
            ->get();

        return view('batch-feedback-questions.index', compact(
            'batch',
            'questions'
        ));
    }

    public function create(Batch $batch)
    {
        return view('batch-feedback-questions.create', compact('batch'));
    }

    public function store(Request $request, Batch $batch)
    {
        $request->validate([
            'question' => 'required|string',
            'type'     => 'required|in:trainer,learner',
        ]);

        $batch->feedbackQuestions()->create($request->only([
            'question',
            'type'
        ]));

        return redirect()
            ->route('batch-feedback-questions.index', $batch->id)
            ->with('success', 'Question added successfully');
    }

    public function edit(Batch $batch, BatchFeedbackQuestion $question)
    {
        return view('batch-feedback-questions.edit', compact(
            'batch',
            'question'
        ));
    }

    public function update(Request $request, Batch $batch, BatchFeedbackQuestion $question)
    {
        $request->validate([
            'question' => 'required|string',
            'type'     => 'required|in:trainer,learner',
        ]);

        $question->update($request->only([
            'question',
            'type'
        ]));

        return redirect()
            ->route('batch-feedback-questions.index', $batch->id)
            ->with('success', 'Question updated successfully');
    }

    public function destroy(Batch $batch, BatchFeedbackQuestion $question)
    {
        $question->delete();

        return back()->with('success', 'Question deleted');
    }

    public function loadDefault(Batch $batch)
    {
        $defaults = DefaultFeedback::all();

        $inserted = 0;

        foreach ($defaults as $df) {

            $exists = BatchFeedbackQuestion::where('batch_id', $batch->id)
                ->where('question', $df->question)
                ->where('type', $df->type)
                ->exists();

            if (! $exists) {
                BatchFeedbackQuestion::create([
                    'batch_id' => $batch->id,
                    'question' => $df->question,
                    'type'     => $df->type,
                ]);

                $inserted++;
            }
        }

        return redirect()
            ->route('batch-feedback-questions.index', $batch->id)
            ->with('success', "{$inserted} default feedback questions loaded successfully.");
    }
}
