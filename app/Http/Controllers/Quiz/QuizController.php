<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CourseTopic;
use App\Models\Question;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('batch')
            ->withCount('questions')
            ->latest()
            ->get();

        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $batches = Batch::pluck('name', 'id');
        return view('quizzes.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'quiz_type' => 'required|in:daily,weekly,monthly,need based',
            'max_attempts' => 'required|integer|min:1|max:3',
            'question_per_page' => 'required|integer|min:1|max:3',
            'visible_start_date' => 'required|date',
            'visible_start_time' => 'required',
            'visible_end_date' => 'required|date',
            'visible_end_time' => 'required',
        ]);

        $start = Carbon::parse($request->visible_start_date . ' ' . $request->visible_start_time);
        $end   = Carbon::parse($request->visible_end_date . ' ' . $request->visible_end_time);

        if ($end->lte($start)) {
            return back()
                ->withErrors([
                    'visible_end_date' => 'End date & time must be greater than start date & time.',
                    'visible_end_time' => 'End time must be greater than start time.',
                ])
                ->withInput();
        }

        Quiz::create([
            'batch_id' => $request->batch_id,
            'title' => $request->title,
            'quiz_type' => $request->quiz_type,
            'minimum_passing_percentage' => $request->minimum_passing_percentage ?? 70,
            'time_limit_minutes' => $request->time_limit_minutes,
            'max_attempts' => $request->max_attempts,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'show_results_immediately' => $request->boolean('show_results_immediately', true),
            'question_per_page' => $request->question_per_page,
            'visible_start_date' => $request->visible_start_date,
            'visible_start_time' => $request->visible_start_time,
            'visible_end_date' => $request->visible_end_date,
            'visible_end_time' => $request->visible_end_time,
            'difficulty_level' => $request->difficulty_level ?? 'easy',
            'status' => $request->status ?? 'inactive',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function edit(Quiz $quiz)
    {
        $batches = Batch::pluck('name', 'id');
        return view('quizzes.edit', compact('quiz', 'batches'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'quiz_type' => 'required|in:daily,weekly,monthly,need based',
            'max_attempts' => 'required|integer|min:1|max:3',
            'question_per_page' => 'required|integer|min:1|max:3',
            'visible_start_date' => 'required|date',
            'visible_start_time' => 'required',
            'visible_end_date' => 'required|date',
            'visible_end_time' => 'required',
        ]);

        $start = Carbon::parse($request->visible_start_date . ' ' . $request->visible_start_time);
        $end   = Carbon::parse($request->visible_end_date . ' ' . $request->visible_end_time);

        if ($end->lte($start)) {
            return back()
                ->withErrors([
                    'visible_end_date' => 'End date & time must be greater than start date & time.',
                    'visible_end_time' => 'End time must be greater than start time.',
                ])
                ->withInput();
        }

        $quiz->update([
            'batch_id' => $request->batch_id,
            'title' => $request->title,
            'quiz_type' => $request->quiz_type,
            'minimum_passing_percentage' => $request->minimum_passing_percentage ?? 70,
            'time_limit_minutes' => $request->time_limit_minutes,
            'max_attempts' => $request->max_attempts,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'show_results_immediately' => $request->boolean('show_results_immediately', true),
            'question_per_page' => $request->question_per_page,
            'visible_start_date' => $request->visible_start_date,
            'visible_start_time' => $request->visible_start_time,
            'visible_end_date' => $request->visible_end_date,
            'visible_end_time' => $request->visible_end_time,
            'difficulty_level' => $request->difficulty_level ?? 'easy',
            'status' => $request->status ?? 'inactive',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Add Questions Page
     */
    public function addQuestions($quizId)
    {
        $quiz = Quiz::with(['questions', 'batch.courses'])
            ->findOrFail($quizId);

        $quizQuestionIds = $quiz->questions->pluck('id')->toArray();

        $hasAttempts = $quiz->attempts()->exists();

        $course = $quiz->batch?->courses->first(); // ✅ correct relation

        if (!$course) {
            abort(404, 'Course not found for this quiz.');
        }

        return view('quizzes.add-questions', compact(
            'quiz',
            'quizQuestionIds',
            'course',
            'hasAttempts'
        ));
    }

    /**
     * Topics API (FIXED)
     */
    public function topicsByCourse(Quiz $quiz)
    {
        $quiz->load('batch.courses');

        $course = $quiz->batch?->courses->first();

        if (!$course) {
            return response()->json([]);
        }

        return CourseTopic::where('course_id', $course->id)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    public function questionsByTopic(CourseTopic $topic)
    {
        return Question::with('options')
            ->where('topic_id', $topic->id)
            ->where('is_active', true)
            ->latest()
            ->paginate(10);
    }

    public function storeQuestions(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_ids' => 'nullable|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $newQuestionIds = $request->question_ids ?? [];

        $existingQuestionIds = $quiz->questions()
            ->pluck('questions.id')
            ->toArray();

        if ($quiz->attempts()->exists()) {

            $removed = array_diff($existingQuestionIds, $newQuestionIds);

            if (!empty($removed)) {
                return back()->withErrors(
                    'You cannot remove questions because this quiz already has attempts.'
                );
            }

            $quiz->questions()->syncWithoutDetaching($newQuestionIds);
        } else {
            $quiz->questions()->sync($newQuestionIds);
        }

        return redirect()->route('quizzes.index')
            ->with('success', 'Questions added to quiz successfully.');
    }

    public function viewQuestions(Quiz $quiz)
    {
        $quiz->load('batch.courses'); // ✅ fixed

        $questions = $quiz->questions()
            ->with(['topic', 'options'])
            ->latest()
            ->paginate(10);

        return view('quizzes.view-questions', compact('quiz', 'questions'));
    }
}
