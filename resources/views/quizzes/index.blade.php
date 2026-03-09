@extends('layouts.app')

@section('title', 'Quizzes | Unibs LMS')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Quizzes</h3>
        <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white w-100" id="listTable">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Batch</th>
                    <th>Quiz Type</th>
                    <th width="150">Visible Time</th>
                    <th width="90">Questions</th>
                    <th>Status</th>
                    <th width="110">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Title', 'Batch', 'Type', 'Visible Time', 'Questions', 'Status'] as $label)
                        <th>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control column-search" placeholder="{{ $label }}">
                                <span class="input-group-text clear-input">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                        </th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($quizzes as $quiz)
                    <tr>
                        <td>{{ $quiz->title }}</td>
                        <td>{{ $quiz->batch?->name ?? '-' }}</td>
                        <td class="text-center">{{ ucfirst($quiz->quiz_type) }}</td>
                        <td>
                            @if ($quiz->visible_start_date || $quiz->visible_end_date)
                                <small class="d-block">
                                    {{-- <strong>From:</strong> --}}
                                    {{ $quiz->visible_start_date ? \Carbon\Carbon::parse($quiz->visible_start_date)->format('d M Y') : '-' }}
                                    @if ($quiz->visible_start_time)
                                        {{ \Carbon\Carbon::parse($quiz->visible_start_time)->format('h:i A') }}
                                    @endif
                                </small>
                                <small class="d-block">
                                    {{-- <strong>To:</strong> --}}
                                    {{ $quiz->visible_end_date ? \Carbon\Carbon::parse($quiz->visible_end_date)->format('d M Y') : '-' }}
                                    @if ($quiz->visible_end_time)
                                        {{ \Carbon\Carbon::parse($quiz->visible_end_time)->format('h:i A') }}
                                    @endif
                                </small>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('quizzes.questions.view', $quiz) }}"
                                class="badge bg-info text-decoration-none">
                                {{ $quiz->questions_count }} <i class="fa fa-eye"></i>
                            </a>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-{{ $quiz->status === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($quiz->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <a href="{{ route('quizzes.questions', $quiz) }}" class="btn btn-outline-info btn-sm"
                                title="Add Questions">
                                <i class="fa-solid fa-circle-question"></i>
                            </a>


                            <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this quiz?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
