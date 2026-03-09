@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Pending Quiz Reviews</h3>

    @if($attempts->isEmpty())
        <div class="alert alert-info">
            No quizzes pending review 🎉
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Quiz</th>
                    <th>Started</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->user->name }}</td>
                        <td>{{ $attempt->quiz->title }}</td>
                        <td>{{ $attempt->started_at?->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('trainer.quiz-reviews.show', $attempt) }}"
                               class="btn btn-sm btn-primary">
                                Review
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
