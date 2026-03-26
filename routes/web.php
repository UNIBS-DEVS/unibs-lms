<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Batch\BatchController;
use App\Http\Controllers\Batch\BatchCourseController;
use App\Http\Controllers\Batch\BatchLearnerController;
use App\Http\Controllers\Batch\BatchTocController;
use App\Http\Controllers\Batch\BatchTrainerController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseTopicController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Feedback\BatchFeedbackController;
use App\Http\Controllers\Feedback\BatchFeedbackQuestionController;
use App\Http\Controllers\Feedback\DefaultFeedbackController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\Quiz\QuizController;
use App\Http\Controllers\Quiz\QuizAttemptController;
use App\Http\Controllers\Reports\AttendanceReportController;
use App\Http\Controllers\Reports\PerformanceReportController;
use App\Http\Controllers\Reports\QuizReportController;
use App\Http\Controllers\Reports\FeedbackReportController;
use App\Http\Controllers\Session\BatchSessionController;
use App\Http\Controllers\Session\SessionAttendanceController;
use App\Http\Controllers\TrainerQuizController;
use App\Http\Controllers\UserController;
use App\Models\Batch;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::redirect('/', '/login');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        Route::resource('users', UserController::class);
        Route::resource('courses', CourseController::class);
        Route::resource('courses.topics', CourseTopicController::class);

        // Admin full BATCHES access (except view)
        Route::resource('batches', BatchController::class)->except(['index', 'show']);

        // Admin full TOC access (except view)
        Route::resource('batches.toc', BatchTocController::class)->except(['index', 'show']);

        Route::resource('batch-courses', BatchCourseController::class);
        Route::resource('batch-trainers', BatchTrainerController::class);
        Route::resource('batch-learners', BatchLearnerController::class);
    });


    /*
    |--------------------------------------------------------------------------
    | VIEW ACCESS (ADMIN + TRAINER + LEARNER)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,trainer,learner')->group(function () {

        // BATCHES
        Route::get('batches', [BatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [BatchController::class, 'show'])->name('batches.show');

        // BATCH TOC
        Route::get('batches/{batch}/toc', [BatchTocController::class, 'index'])
            ->name('batches.toc.index');

        Route::get('batches/{batch}/toc/{toc}', [BatchTocController::class, 'show'])
            ->name('batches.toc.show');

        // BATCH PROGRESS 
        Route::get('progress', [BatchTocController::class, 'progressIndex'])
            ->name('progress.index');
    });



    /*
    |--------------------------------------------------------------------------
    | BATCH PROGRESS (VIEW FOR ALL, EDIT ONLY ADMIN/TRAINER)
    |--------------------------------------------------------------------------
    */
    // Edit only for admin/trainer
    Route::middleware('role:admin,trainer')->group(function () {
        Route::get('progress/{batch}/{toc}/edit', [BatchTocController::class, 'progressEdit'])
            ->name('progress.edit');

        Route::put('progress/{batch}/{toc}', [BatchTocController::class, 'progressUpdate'])
            ->name('progress.update');
    });
    /*
    |--------------------------------------------------------------------------
    | ADMIN + TRAINER
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin.trainer')->group(function () {

        Route::resource('batch-sessions', BatchSessionController::class);

        Route::get('/batch-sessions/{session}/attendance', [SessionAttendanceController::class, 'index'])->name('sessions.attendance.index');
        Route::post('/batch-sessions/{session}/attendance', [SessionAttendanceController::class, 'store'])->name('sessions.attendance.store');
        Route::post('/batch-sessions/{session}/attendance/email', [SessionAttendanceController::class, 'sendAttendanceEmail'])->name('sessions.attendance.email');

        Route::get('reports/attendance/filter', [AttendanceReportController::class, 'filter'])->name('reports.attendance.filter');

        Route::resource('questions', QuestionController::class);
        Route::resource('quizzes', QuizController::class);

        Route::get('quizzes/{quiz}/questions', [QuizController::class, 'addQuestions'])->name('quizzes.questions');
        Route::post('quizzes/{quiz}/questions', [QuizController::class, 'storeQuestions'])->name('quizzes.store-questions');
        Route::get('quizzes/{quiz}/questions/view', [QuizController::class, 'viewQuestions'])->name('quizzes.questions.view');

        Route::get('quizzes/{quiz}/topics', [QuizController::class, 'topicsByCourse'])->name('quizzes.topics');
        Route::get('topics/{topic}/questions', [QuizController::class, 'questionsByTopic'])->name('topics.questions');

        Route::get('/courses/{course}/topics-list', [CourseTopicController::class, 'list'])->name('courses.topics.list');

        Route::get('/batches/{batch}/details', function (Batch $batch) {
            return response()->json([
                'trainers' => $batch->trainers()->where('users.role', 'trainer')->select('users.id', 'users.name')->get(),
                'courses' => $batch->courses()->select('courses.id', 'courses.name')->get(),
            ]);
        })->name('batches.details');
    });

    /*
    |--------------------------------------------------------------------------
    | LEARNER QUIZ ATTEMPTS
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:learner,admin')->group(function () {

        Route::get('/attempt-quizzes', [QuizAttemptController::class, 'index'])->name('quiz.attempt.index');
        Route::post('/attempt-quizzes/{quiz}', [QuizAttemptController::class, 'start'])->name('quiz.attempt.start');

        Route::get('/quiz-attempt/{attempt}/question/{page}', [QuizAttemptController::class, 'showQuestion'])->name('quiz.question.show');
        Route::post('/quiz-attempt/{attempt}/question/{page}', [QuizAttemptController::class, 'saveAnswer'])->name('quiz.question.save');

        Route::get('/quiz-attempt/{attempt}/result', [QuizAttemptController::class, 'result'])->name('quiz.attempt.result');
        Route::post('/quiz-attempt/{attempt}/exit-submit', [QuizAttemptController::class, 'exitAndSubmit'])->name('quiz.attempt.exit.submit');
    });

    /*
    |--------------------------------------------------------------------------
    | TRAINER REVIEW
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:trainer,admin'])->prefix('trainer')->name('trainer.')->group(function () {

        Route::get('quiz-reviews', [TrainerQuizController::class, 'index'])->name('quiz-reviews.index');
        Route::get('quiz-reviews/{attempt}', [TrainerQuizController::class, 'show'])->name('quiz-reviews.show');
        Route::post('quiz-reviews/answer/{answer}', [TrainerQuizController::class, 'reviewAnswer'])->name('quiz-reviews.answer');
        Route::post('quiz-reviews/publish/{attempt}', [TrainerQuizController::class, 'publish'])->name('quiz-reviews.publish');
    });

    Route::get('/quiz-answer/{answer}/file', [TrainerQuizController::class, 'viewFile'])->name('trainer.quiz-answer.file');

    /*
    |--------------------------------------------------------------------------
    | FEEDBACK MANAGEMENT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,trainer'])->prefix('feedback')->name('feedback.')->group(function () {

        Route::resource('trainer', DefaultFeedbackController::class)
            ->parameters(['trainer' => 'feedback'])
            ->except(['show']);

        Route::get('/share/trainers/{batch}', [BatchFeedbackController::class, 'trainers'])->name('share.trainers');

        Route::get('/share/learners/{batch}', [BatchFeedbackController::class, 'learners'])->name('share.learners');
    });

    /*
    |--------------------------------------------------------------------------
    | SHARE FEEDBACK
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,trainer,learner'])->prefix('feedback')->name('feedback.')->group(function () {

        Route::prefix('share')->name('share.')->group(function () {

            Route::get('/', [BatchFeedbackController::class, 'index'])->name('index');
            Route::post('/', [BatchFeedbackController::class, 'store'])->name('store');

            Route::get('/learners/{batch}', [BatchFeedbackController::class, 'learners'])->name('feedback.share.learners');

            Route::get('/trainers/{batch}', [BatchFeedbackController::class, 'trainers'])->name('feedback.share.trainers');

            Route::get('/questions', [BatchFeedbackController::class, 'questions'])->name('questions');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | BATCH FEEDBACK QUESTIONS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,trainer'])->group(function () {

        Route::prefix('batches/{batch}')->group(function () {

            Route::get('feedback-questions', [BatchFeedbackQuestionController::class, 'index'])->name('batch-feedback-questions.index');
            Route::get('feedback-questions/create', [BatchFeedbackQuestionController::class, 'create'])->name('batch-feedback-questions.create');
            Route::post('feedback-questions', [BatchFeedbackQuestionController::class, 'store'])->name('batch-feedback-questions.store');

            Route::get('feedback-questions/{question}/edit', [BatchFeedbackQuestionController::class, 'edit'])->name('batch-feedback-questions.edit');
            Route::put('feedback-questions/{question}', [BatchFeedbackQuestionController::class, 'update'])->name('batch-feedback-questions.update');
            Route::delete('feedback-questions/{question}', [BatchFeedbackQuestionController::class, 'destroy'])->name('batch-feedback-questions.destroy');

            Route::post('feedback-questions/load-default', [BatchFeedbackQuestionController::class, 'loadDefault'])->name('batch-feedback-questions.load-default');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,trainer'])->prefix('reports')->name('reports.')->group(function () {

        Route::get('attendance', [AttendanceReportController::class, 'index'])->name('attendance.index');
        Route::get('attendance/excel', [AttendanceReportController::class, 'exportExcel'])->name('attendance.excel');
        Route::get('attendance/pdf', [AttendanceReportController::class, 'exportPdf'])->name('attendance.pdf');

        Route::get('quiz', [QuizReportController::class, 'index'])->name('quiz.index');
        Route::get('quiz/filter', [QuizReportController::class, 'filter'])->name('quiz.filter');
        Route::get('quiz/by-batch/{batch}', [QuizReportController::class, 'getQuizzesByBatch'])->name('quiz.byBatch');
        Route::get('quiz/excel', [QuizReportController::class, 'exportExcel'])->name('quiz.excel');
        Route::get('quiz/pdf', [QuizReportController::class, 'exportPdf'])->name('quiz.pdf');

        Route::get('feedback', [FeedbackReportController::class, 'index'])->name('feedback.index');
        Route::get('feedback/filter', [FeedbackReportController::class, 'filter'])
            ->name('feedback.filter');
        Route::get('/feedback/details/{id}', [FeedbackReportController::class, 'details']);
        Route::get('feedback/excel', [FeedbackReportController::class, 'exportExcel'])->name('feedback.excel');
        Route::get('feedback/pdf', [FeedbackReportController::class, 'exportPdf'])->name('feedback.pdf');

        Route::get(
            'feedback/details/{id}',
            [FeedbackReportController::class, 'details']
        )->name('reports.feedback.details');


        Route::get('performance', [PerformanceReportController::class, 'index'])->name('performance.index');
        Route::get('performance/filter', [PerformanceReportController::class, 'filter'])->name('performance.filter');

        Route::get('performance/excel', [PerformanceReportController::class, 'exportExcel'])->name('performance.excel');

        Route::get('performance/pdf', [PerformanceReportController::class, 'exportPdf'])->name('performance.pdf');
    });
});
