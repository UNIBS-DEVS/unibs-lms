@extends('layouts.app')

@push('styles')
    <style>
        .alert-danger {
            margin-top: 10px;
            margin-bottom: 0;
            padding: 10px 5px;
        }

        .question-box {
            padding: 15px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <form method="POST" action="{{ route('quiz.question.save', [$attempt->id, $page]) }}" enctype="multipart/form-data">
        @csrf

        {{-- TIMER --}}
        @if ($attempt->ends_at)
            <div class="alert alert-info text-center">
                ⏳ Time left: <strong id="timer"></strong>
            </div>
        @endif

        <h6 class="mb-3">
            Page {{ $page }} of {{ $totalPages }}
        </h6>

        {{-- QUESTIONS LOOP --}}
        @foreach ($questions as $index => $question)
            @php
                $qNumber = $startNumber + $index;
                $answer = $answers[$question->id] ?? null;

                $options = $question->options;
                if ($attempt->quiz->shuffle_options) {
                    $options = $options->shuffle();
                }
            @endphp

            <div class="question-box">
                <p>
                    <strong>Q{{ $qNumber }}. {{ $question->question_text }}</strong>
                </p>

                {{-- SINGLE CHOICE --}}
                @if ($question->question_type === 'single_choice')
                    @foreach ($options as $option)
                        <div class="form-check">
                            <input type="radio" name="answer_{{ $question->id }}" value="{{ $option->id }}"
                                class="form-check-input"
                                {{ in_array($option->id, $answer->answer_options ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                {{ $option->option_text }}
                            </label>
                        </div>
                    @endforeach
                @endif

                {{-- MULTIPLE CHOICE --}}
                @if ($question->question_type === 'multiple_choice')
                    @foreach ($options as $option)
                        <div class="form-check">
                            <input type="checkbox" name="answer_{{ $question->id }}[]" value="{{ $option->id }}"
                                class="form-check-input"
                                {{ in_array($option->id, $answer->answer_options ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                {{ $option->option_text }}
                            </label>
                        </div>
                    @endforeach
                @endif

                {{-- TEXT --}}
                @if ($question->question_type === 'text')
                    <textarea name="answer_{{ $question->id }}" class="form-control" rows="4">{{ $answer->answer_text ?? '' }}</textarea>
                @endif

                {{-- FILE --}}
                @if ($question->question_type === 'file')
                    <input type="file" name="answer_{{ $question->id }}" class="form-control">

                    @if ($question->fileSettings)
                        <small class="text-muted">
                            Allowed:
                            {{ $question->fileSettings->allowed_file_types ?? 'Any' }},
                            Max size:
                            {{ $question->fileSettings->max_file_size_mb }} MB
                        </small>
                    @endif
                @endif
            </div>
        @endforeach

        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ACTION BUTTONS --}}
        <div class="d-flex justify-content-between mt-4">
            <div>
                @if ($page > 1)
                    <a href="{{ route('quiz.question.show', [$attempt->id, $page - 1]) }}" class="btn btn-secondary">
                        Prev
                    </a>
                @endif

                <button class="btn btn-primary">
                    {{ $page === $totalPages ? 'Finish Quiz' : 'Next' }}
                </button>
            </div>

            <div>
                <button type="button" class="btn btn-danger" onclick="exitQuiz()">
                    Exit & Submit
                </button>
            </div>
        </div>
    </form>

    {{-- HIDDEN EXIT FORM --}}
    <form id="exit-form" method="POST" action="{{ route('quiz.attempt.exit.submit', $attempt->id) }}"
        style="display:none;">
        @csrf
    </form>
@endsection

@push('scripts')
    @if ($attempt->ends_at)
        <script>
            const endTime = new Date("{{ $attempt->ends_at->toIso8601String() }}").getTime();
            const timer = setInterval(() => {
                const now = new Date().getTime();
                const diff = endTime - now;

                if (diff <= 0) {
                    clearInterval(timer);
                    alert('Time is up! Submitting quiz.');
                    document.getElementById('exit-form').submit();
                    return;
                }

                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                document.getElementById('timer').innerText =
                    minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }, 1000);
        </script>
    @endif

    <script>
        function exitQuiz() {
            if (confirm('Exit quiz and submit your answers? This action cannot be undone.')) {
                document.getElementById('exit-form').submit();
            }
        }
    </script>
@endpush
