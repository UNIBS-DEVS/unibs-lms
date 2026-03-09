<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseTopic;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\TextQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions
     */
    public function index(Request $request)
    {
        $questions = Question::with(['course', 'topic'])
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->topic_id, fn($q) => $q->where('topic_id', $request->topic_id))
            ->when($request->question_type, fn($q) => $q->where('question_type', $request->question_type))
            ->latest()
            ->paginate(10);

        return view('questions.index', [
            'questions' => $questions,
            'courses' => Course::all(),
            'topics' => CourseTopic::all(),
        ]);
    }

    /**
     * Show form to create a question
     */
    public function create()
    {
        return view('questions.create', [
            'courses' => Course::orderBy('id')->get(),
            'topics'  => CourseTopic::orderBy('id')->get(),
        ]);
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'required|exists:course_topics,id',
            'question_type' => 'required|in:single_choice,multiple_choice,text,file',
            'question_text' => 'required|string',
            'max_marks' => 'required|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
            'marking_type' => 'required|in:automatic,manual',

            // File question validation
            'allowed_file_types' => 'nullable|string',
            'max_file_size_mb'   => 'nullable|integer|min:1',
            'question_file'      => 'nullable|file',
        ]);

        DB::transaction(function () use ($request, $validated) {

            // Create question
            $question = Question::create([
                'course_id' => $validated['course_id'],
                'topic_id' => $validated['topic_id'],
                'question_type' => $validated['question_type'],
                'question_text' => $validated['question_text'],
                'max_marks' => $validated['max_marks'],
                'negative_marks' => $validated['negative_marks'] ?? 0,
                'marking_type' => $validated['marking_type'],
                'is_active' => true,
            ]);

            // Handle MCQs 
            if (in_array($question->question_type, ['single_choice', 'multiple_choice'])) {

                $correctIndex = $request->correct_option;

                foreach ($request->options ?? [] as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['text'],
                        'is_correct' =>
                        $question->question_type === 'single_choice'
                            ? ((string)$index === (string)$correctIndex)
                            : isset($option['is_correct']),
                    ]);
                }
            }

            /* =========================
            File Question Settings
         ========================== */
            if ($question->question_type === 'file') {

                // normalize allowed types: ".PDF, docx" → "pdf,docx"
                $allowedTypes = collect(explode(',', $request->allowed_file_types))
                    ->map(fn($t) => strtolower(trim(str_replace('.', '', $t))))
                    ->filter()
                    ->implode(',');

                $filePath = null;

                if ($request->hasFile('question_file')) {
                    $filePath = $request->file('question_file')
                        ->store('question_files', 'public');
                }

                $question->fileSettings()->create([
                    'allowed_file_types' => $allowedTypes,
                    'max_file_size_mb'   => $request->max_file_size_mb ?? 2,
                    'file_path'          => $filePath,
                ]);
            }
        });


        return redirect()->route('questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function edit(Question $question)
    {
        $question->load([
            'options',
            'fileSettings', // 👈 ADD THIS
        ]);

        return view('questions.edit', [
            'question' => $question,
            'courses'  => Course::all(),
            'topics'   => CourseTopic::all(),
        ]);
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'course_id'        => 'required|exists:courses,id',
            'topic_id'         => 'required|exists:course_topics,id',
            'question_type'    => 'required|in:single_choice,multiple_choice,text,file',
            'question_text'    => 'required|string',
            'max_marks'        => 'required|numeric|min:0',
            'negative_marks'   => 'nullable|numeric|min:0',
            'marking_type'     => 'required|in:automatic,manual',

            // file question
            'allowed_file_types' => 'nullable|string',
            'max_file_size_mb'   => 'nullable|integer|min:1',
            'question_file'      => 'nullable|file',
        ]);

        DB::transaction(function () use ($request, $validated, $question) {

            /* =========================
               Update main question
            ========================== */
            $question->update([
                'course_id'      => $validated['course_id'],
                'topic_id'       => $validated['topic_id'],
                'question_type'  => $validated['question_type'],
                'question_text'  => $validated['question_text'],
                'max_marks'      => $validated['max_marks'],
                'negative_marks' => $validated['negative_marks'] ?? 0,
                'marking_type'   => $validated['marking_type'],
            ]);

            /* =========================
               Reset old relations
            ========================== */
            $question->options()->delete();

            /* =========================
               MCQ handling
            ========================== */
            if (in_array($question->question_type, ['single_choice', 'multiple_choice'])) {

                $correctIndex = $request->correct_option;

                foreach ($request->options ?? [] as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['text'],
                        'is_correct'  =>
                        $question->question_type === 'single_choice'
                            ? ((string) $index === (string) $correctIndex)
                            : isset($option['is_correct']),
                    ]);
                }
            }

            /* =========================
                File Question Settings
            ========================== */
            if ($question->question_type === 'file') {

                $allowedTypes = collect(explode(',', $request->allowed_file_types))
                    ->map(fn($t) => strtolower(trim(str_replace('.', '', $t))))
                    ->filter()
                    ->implode(',');

                $filePath = $question->fileSettings?->file_path;

                if ($request->hasFile('question_file')) {

                    if ($filePath) {
                        Storage::disk('public')->delete($filePath);
                    }

                    $filePath = $request->file('question_file')
                        ->store('question_files', 'public');
                }

                $question->fileSettings()->updateOrCreate(
                    ['question_id' => $question->id],
                    [
                        'allowed_file_types' => $allowedTypes,
                        'max_file_size_mb'   => $request->max_file_size_mb ?? 2,
                        'file_path'          => $filePath,
                    ]
                );
            }
        });
        return redirect()
            ->route('questions.index')
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question
     */
    public function destroy(Question $question)
    {
        DB::transaction(function () use ($question) {

            // delete file if exists
            if ($question->question_file_path) {
                Storage::disk('public')->delete($question->question_file_path);
            }

            // relations auto delete if FK cascade exists
            $question->options()->delete();
            // $question->textAnswer()?->delete();

            $question->delete();
        });

        return redirect()
            ->route('questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
