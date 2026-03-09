<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\DefaultFeedback;
use Illuminate\Http\Request;

class DefaultFeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = DefaultFeedback::latest()->get();
        return view('feedback.management.index', compact('feedbacks'));
    }

    public function create()
    {
        return view('feedback.management.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'type'     => 'required|in:trainer,learner',
        ]);

        DefaultFeedback::create($request->only('question', 'type', 'category'));

        return redirect()
            ->route('feedback.trainer.index')
            ->with('success', 'Feedback question created');
    }

    public function edit(DefaultFeedback $feedback)
    {
        return view('feedback.management.edit', compact('feedback'));
    }

    public function update(Request $request, DefaultFeedback $feedback)
    {
        $request->validate([
            'question' => 'required|string',
            'type'     => 'required|in:trainer,learner',
        ]);

        $feedback->update($request->only('question', 'type'));

        return redirect()
            ->route('feedback.trainer.index')
            ->with('success', 'Feedback updated');
    }

    public function destroy(DefaultFeedback $feedback)
    {
        $feedback->delete();

        return redirect()
            ->route('feedback.trainer.index')
            ->with('success', 'Feedback deleted');
    }
}
