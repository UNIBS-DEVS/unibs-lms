@extends('layouts.app')

@section('content')
    <div class="container">

        <h4 class="mb-3">Quiz Result</h4>

        {{-- SUMMARY --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5>{{ $attempt->quiz->title }}</h5>

                <p class="mb-1">
                    <strong>Status:</strong>
                    <span class="badge bg-info">
                        {{ strtoupper(str_replace('_', ' ', $attempt->status)) }}
                    </span>
                </p>

                <p class="mb-1">
                    <strong>Total Score:</strong>
                    {{ $attempt->score ?? 'Pending Review' }}
                </p>

                @php
                    $summary = $attempt->attemptSummary();
                @endphp

                <hr>

                <div class="row text-center">
                    <div class="col">
                        <span class="badge bg-primary">Total</span>
                        <div>{{ $summary['total'] }}</div>
                    </div>

                    <div class="col">
                        <span class="badge bg-success">Answered</span>
                        <div>{{ $summary['answered'] }}</div>
                    </div>

                    <div class="col">
                        <span class="badge bg-danger">Skipped</span>
                        <div>{{ $summary['skipped'] }}</div>
                    </div>

                    <div class="col">
                        <span class="badge bg-success">Correct</span>
                        <div>{{ $summary['correct'] }}</div>
                    </div>

                    <div class="col">
                        <span class="badge bg-warning text-dark">Wrong</span>
                        <div>{{ $summary['wrong'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ANSWERS --}}
        @foreach ($attempt->answers as $index => $answer)
            @php
                $question = $answer->question;
            @endphp

            <div class="card mb-3">
                <div class="card-header">
                    <strong>Q{{ $index + 1 }}.</strong>
                    {{ $question->question_text }}
                </div>

                <div class="card-body">

                    <p><strong>Your Answer:</strong></p>

                    {{-- SINGLE / MULTIPLE --}}
                    @if (in_array($question->question_type, ['single_choice', 'multiple_choice']))
                        <ul>
                            @foreach ($question->options as $option)
                                <li>
                                    {{ $option->option_text }}

                                    @if (in_array($option->id, $answer->answer_options ?? []))
                                        <strong>(Selected)</strong>
                                        @if (!$option->is_correct)
                                            <span class="text-danger"><strong>(Wrong)</strong></span>
                                        @endif
                                    @endif

                                    @if ($option->is_correct)
                                        <strong class="text-success">(Correct)</strong>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- TEXT --}}
                    @if ($question->question_type === 'text')
                        <div class="border p-2">
                            {{ $answer->answer_text ?: 'Not Answered' }}
                        </div>
                    @endif

                    {{-- FILE --}}
                    @if ($question->question_type === 'file')
                        @if ($answer->answer_file)
                            <a href="{{ route('trainer.quiz-answer.file', $answer->id) }}" target="_blank"
                                class="btn btn-sm btn-info">
                                View File
                            </a>
                        @else
                            <span class="text-danger">Not Answered</span>
                        @endif
                    @endif

                    <hr>

                    {{-- RESULT --}}
                    @if ($answer->is_correct === true)
                        <span class="badge bg-success">
                            Correct (+{{ $answer->marks_obtained }})
                        </span>
                    @elseif ($answer->is_correct === false)
                        <span class="badge bg-danger">
                            Wrong ({{ $answer->marks_obtained }})
                        </span>
                    @else
                        <span class="badge bg-warning">
                            Pending Review / Not Answered
                        </span>
                    @endif

                </div>
            </div>
        @endforeach

        <a href="{{ route('quiz.attempt.index') }}" class="btn btn-secondary">
            Back to Quizzes
        </a>

    </div>
@endsection
