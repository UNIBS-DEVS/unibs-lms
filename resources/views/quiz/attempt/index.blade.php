@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="mb-3">Attempt Quiz</h4>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center" width="10">S.no.</th>
                    <th>Quiz Name</th>
                    <th class="text-center" width="20%">Quiz Time</th>
                    <th class="text-center">Questions</th>
                    <th class="text-center" width="200">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($quizzes as $index => $quiz)
                    @php
                        $attempts = $quiz->attempts;
                        $usedAttempts = $attempts->count();
                        $maxAttempts = $quiz->max_attempts;
                        $activeAttempt = $attempts->where('status', 'in_progress')->first();
                        $lastAttempt = $attempts->first(); // latest attempt
                        $canAttemptAgain = !$maxAttempts || $usedAttempts < $maxAttempts;
                    @endphp

                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <td>
                            <strong>{{ $quiz->title }}</strong>

                            @if ($maxAttempts)
                                <br>
                                <small class="text-muted">
                                    Attempts: {{ $usedAttempts }} / {{ $maxAttempts }}
                                </small>
                            @endif
                        </td>

                        <td class="text-center">
                            {{ $quiz->time_limit_minutes }} Minutes
                        </td>

                        <td class="text-center">
                            {{ $quiz->questions_count }}
                        </td>

                        <td class="text-center">

                            {{-- ❌ No questions --}}
                            @if ($quiz->questions_count === 0)
                                <button class="btn btn-sm btn-secondary" disabled>
                                    No Questions
                                </button>

                                {{-- 🔁 Resume --}}
                            @elseif ($activeAttempt)
                                <a href="{{ route('quiz.question.show', [$activeAttempt->id, 1]) }}"
                                    class="btn btn-sm btn-info mb-1">
                                    Resume
                                </a>
                            @else
                                {{-- 🆕 Start / Retake --}}
                                @if ($canAttemptAgain)
                                    <form method="POST" action="{{ route('quiz.attempt.start', $quiz->id) }}"
                                        class="mb-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            {{ $usedAttempts > 0 ? 'Retake Quiz' : 'Start Quiz' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary mt-1 d-inline-block">
                                        Attempts Exhausted
                                    </span>
                                @endif

                                {{-- 🏆 Last Attempt Status --}}
                                @if ($lastAttempt)
                                    @if (in_array($lastAttempt->status, ['completed_auto', 'result_published']))
                                        <a href="{{ route('quiz.attempt.result', $lastAttempt->id) }}"
                                            class="btn btn-sm btn-success mt-1">
                                            View Result
                                        </a>
                                    @elseif ($lastAttempt->status === 'pending_manual_review')
                                        <span class="badge bg-info mb-1 d-inline-block">
                                            Pending Review
                                        </span>
                                    @endif
                                @endif
                            @endif

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            No quizzes available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
