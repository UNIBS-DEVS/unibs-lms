<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Batch;
use App\Models\QuizAttempt;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\QuizAttemptAnswer;
use App\Models\FileQuestionSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuizAttemptController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $quizzes = Quiz::withCount('questions')
            ->with([
                'attempts' => function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->latest();
                }
            ])
            ->where('status', 'active')
            ->when($user->role === 'learner', function ($query) use ($user) {
                $query->whereIn(
                    'batch_id',
                    $user->batches()->pluck('batches.id')
                );
            })
            ->get();

        return view('quiz.attempt.index', compact('quizzes'));
    }

    public function start(Request $request, Quiz $quiz)
    {
        $user = Auth::user();

        // 🔒 Batch access check
        if ($user->role === 'learner') {
            $isAssigned = $user->batches()
                ->where('batches.id', $quiz->batch_id)
                ->exists();

            abort_if(!$isAssigned, 403, 'You are not assigned to this batch.');
        }

        // 🔁 Resume attempt if exists
        $attempt = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {

            $questionIds = $quiz->questions()->pluck('questions.id')->toArray();

            if ($quiz->shuffle_questions) {
                shuffle($questionIds);
            }

            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'status' => 'in_progress',
                'started_at' => now(),
                'question_order' => $questionIds,
            ]);
        }

        // return redirect()->route('quiz.question.show', [
        //     'attempt' => $attempt->id,
        //     'number' => 1,
        // ]);

        return redirect()->route('quiz.question.show', [
            'attempt' => $attempt->id,
            'page' => 1
        ]);
    }

    public function showQuestion(QuizAttempt $attempt, $page)
    {
        // ⏳ Auto-submit if time over (your existing logic stays)
        if ($attempt->ends_at && now()->greaterThan($attempt->ends_at)) {
            // existing auto submit logic...
        }

        $order = $attempt->question_order;
        abort_if(!$order, 404);

        $perPage = $attempt->quiz->question_per_page ?? 1;

        $totalQuestions = count($order);
        $totalPages = (int) ceil($totalQuestions / $perPage);

        abort_if($page < 1 || $page > $totalPages, 404);

        $offset = ($page - 1) * $perPage;
        $questionIds = array_slice($order, $offset, $perPage);

        $questions = $attempt->quiz->questions()
            ->with(['options', 'fileSettings'])
            ->whereIn('questions.id', $questionIds)
            ->get()
            ->sortBy(fn($q) => array_search($q->id, $questionIds))
            ->values();

        $answers = $attempt->answers()
            ->whereIn('question_id', $questionIds)
            ->get()
            ->keyBy('question_id');

        return view('quiz.attempt.question', [
            'attempt' => $attempt,
            'questions' => $questions,
            'answers' => $answers,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'startNumber' => $offset + 1,
        ]);
    }

    public function saveAnswer(Request $request, QuizAttempt $attempt, $page)
    {
        if ($attempt->isFinalized()) {
            return redirect()->route('quiz.attempt.result', $attempt->id);
        }

        $order = $attempt->question_order;
        $perPage = $attempt->quiz->question_per_page ?? 1;

        $offset = ($page - 1) * $perPage;
        $questionIds = array_slice($order, $offset, $perPage);

        $questions = $attempt->quiz->questions()
            ->with(['options', 'fileSettings'])
            ->whereIn('questions.id', $questionIds)
            ->get();

        foreach ($questions as $question) {

            $field = "answer_{$question->id}";
            $data = [
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ];

            $isCorrect = null;
            $marksObtained = null;

            /* ---------------- STORE ANSWER ---------------- */

            if ($question->question_type === 'single_choice' && $request->filled($field)) {
                $data['answer_options'] = [$request->$field];
            }

            if ($question->question_type === 'multiple_choice' && $request->filled($field)) {
                $data['answer_options'] = $request->$field;
            }

            if ($question->question_type === 'text' && $request->filled($field)) {
                $data['answer_text'] = $request->$field;
            }

            if ($question->question_type === 'file') {

                $setting = $question->fileSettings ?? new FileQuestionSetting([
                    'allowed_file_types' => '', // default empty means no restriction
                    'max_file_size_mb' => 5,
                ]);

                $allowedTypesRaw = $setting->allowed_file_types ?? '';

                $allowedTypes = collect(explode(',', $allowedTypesRaw))
                    ->map(fn($t) => strtolower(trim(str_replace('.', '', $t))))
                    ->filter()
                    ->values()
                    ->all(); // get array of extensions like ['pdf','jpg']


                $maxSizeKb = ($setting->max_file_size_mb ?? 5) * 1024;

                $rules = [
                    $field => ['required', 'file', 'max:' . $maxSizeKb],
                ];

                if (count($allowedTypes) > 0) {
                    $rules[$field][] = 'mimes:' . implode(',', $allowedTypes);
                }

                $messages = [
                    "$field.required" => 'Please upload a file.',
                    "$field.file"     => 'Invalid file.',
                    "$field.max"      => 'File size exceeded.',
                    "$field.mimes"    => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes),
                ];

                $request->validate($rules, $messages);

                $file = $request->file($field);

                $filename = sprintf(
                    '%s-%s-response.%s',
                    $this->cleanFileName($attempt->quiz->id),
                    $this->cleanFileName($question->id),
                    $file->getClientOriginalExtension()
                );

                $data['answer_file'] = $file->storeAs(
                    'quiz/response',
                    $filename,
                    'public'
                );
            }


            /* ---------------- AUTO EVALUATION ---------------- */
            if ($question->marking_type === 'automatic') {

                if ($question->question_type === 'single_choice') {

                    $correct = $question->options
                        ->where('is_correct', 1)
                        ->pluck('id')
                        ->first();

                    $isCorrect = $correct && in_array($correct, $data['answer_options'] ?? []);
                }

                if ($question->question_type === 'multiple_choice') {

                    $correct = $question->options
                        ->where('is_correct', 1)
                        ->pluck('id')
                        ->map(fn($id) => (int) $id)
                        ->sort()
                        ->values()
                        ->toArray();

                    $given = collect($data['answer_options'] ?? [])
                        ->map(fn($id) => (int) $id)
                        ->sort()
                        ->values()
                        ->toArray();

                    $isCorrect = ($correct === $given);
                }


                if ($isCorrect === true) {
                    $marksObtained = (float) $question->max_marks;
                } elseif ($isCorrect === false) {
                    $marksObtained = -1 * (float) $question->negative_marks;
                }
            }

            QuizAttemptAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                array_merge($data, [
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained,
                ])
            );
        }

        /* ---------------- FINAL SUBMIT ---------------- */
        $totalPages = (int) ceil(count($order) / $perPage);

        if ($page >= $totalPages) {

            if ($this->hasAnsweredManualQuestion($attempt)) {
                $attempt->markPendingManual();
            } else {
                $attempt->finalizeScore();
            }


            return redirect()
                ->route('quiz.attempt.index')
                ->with('success', 'Quiz submitted successfully.');
        }

        return redirect()->route('quiz.question.show', [
            'attempt' => $attempt->id,
            'page' => $page + 1
        ]);
    }

    private function calculateAutoScore(QuizAttempt $attempt): int
    {
        $score = 0;

        $answers = $attempt->answers()
            ->with('question.options')
            ->get();

        foreach ($answers as $answer) {

            $question = $answer->question;

            // Skip manual questions
            if ($question->marking_type === 'manual') {
                continue;
            }

            $isCorrect = false;

            /* ---------- SINGLE CHOICE ---------- */
            if ($question->question_type === 'single_choice') {

                $correctOptionId = $question->options
                    ->where('is_correct', 1)
                    ->pluck('id')
                    ->first();

                $given = $answer->answer_options ?? [];

                if ($correctOptionId && in_array($correctOptionId, $given)) {
                    $isCorrect = true;
                }
            }

            /* ---------- MULTIPLE CHOICE ---------- */
            if ($question->question_type === 'multiple_choice') {

                $correct = $question->options
                    ->where('is_correct', 1)
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->toArray();

                $given = collect($answer->answer_options ?? [])
                    ->map(fn($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->toArray();

                if ($correct === $given) {
                    $isCorrect = true;
                }
            }

            /* ---------- APPLY MARKING ---------- */
            if ($isCorrect) {
                $score += (int) $question->max_marks;
            } else {
                $score -= (int) $question->negative_marks;
            }
        }

        // Never allow negative total score
        return max(0, $score);
    }

    public function result(QuizAttempt $attempt)
    {
        // Security
        abort_if(
            $attempt->user_id !== Auth::id(),
            403
        );

        abort_if(
            !in_array($attempt->status, ['completed_auto', 'result_published']),
            403
        );

        $attempt->load([
            'quiz',
            'answers.question',
        ]);

        return view('quiz.attempt.result', compact('attempt'));
    }

    public function exitAndSubmit(QuizAttempt $attempt)
    {
        // Security
        abort_if($attempt->user_id !== Auth::id(), 403);

        // Prevent double submit
        if ($attempt->isFinalized()) {
            return redirect()
                ->route('quiz.attempt.index')
                ->with('info', 'Quiz already submitted.');
        }

        if ($this->hasAnsweredManualQuestion($attempt)) {

            $attempt->markPendingManual();

            return redirect()
                ->route('quiz.attempt.index')
                ->with('success', 'Quiz submitted for trainer review.');
        }


        // Auto only
        $attempt->finalizeScore();

        if ($attempt->shouldShowResultImmediately()) {
            return redirect()->route('quiz.attempt.result', $attempt->id);
        }

        return redirect()
            ->route('quiz.attempt.index')
            ->with('success', 'Quiz submitted successfully.');
    }

    private function cleanFileName(string $name): string
    {
        return Str::of($name)
            ->lower()
            ->replace(['/', '\\'], '-')
            ->replaceMatches('/[^a-z0-9\- ]/', '')
            ->replace(' ', '-')
            ->limit(80, '')
            ->toString();
    }

    private function hasAnsweredManualQuestion(QuizAttempt $attempt): bool
    {
        return $attempt->answers()
            ->whereHas('question', function ($q) {
                $q->whereIn('question_type', ['text', 'file']);
            })
            ->where(function ($q) {
                $q->whereNotNull('answer_text')
                    ->orWhereNotNull('answer_file');
            })->exists();
    }
}
