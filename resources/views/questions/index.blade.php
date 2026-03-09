@extends('layouts.app')

@section('title', 'Question Bank')

@section('content')

    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Question Bank</h4>

            <a href="{{ route('questions.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i>
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('questions.index') }}">
                    <div class="row g-2">

                        <!-- Course -->
                        <div class="col-md-3">
                            <label class="form-label">Course</label>
                            <select name="course_id" class="form-select">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Topic -->
                        <div class="col-md-3">
                            <label class="form-label">Topic</label>
                            <select name="topic_id" class="form-select">
                                <option value="">All Topics</option>
                                @foreach ($topics as $topic)
                                    <option value="{{ $topic->id }}"
                                        {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Question Type -->
                        <div class="col-md-4">
                            <label class="form-label">Question Type</label>
                            <select name="question_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="single_choice"
                                    {{ request('question_type') == 'single_choice' ? 'selected' : '' }}>
                                    Single
                                    Choice
                                </option>
                                <option value="multiple_choice"
                                    {{ request('question_type') == 'multiple_choice' ? 'selected' : '' }}>
                                    Multiple
                                    Choice</option>
                                <option value="text" {{ request('question_type') == 'text' ? 'selected' : '' }}>Text
                                </option>
                                <option value="file" {{ request('question_type') == 'file' ? 'selected' : '' }}>File
                                    Upload
                                </option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-2 d-flex align-items-end justify-content-evenly">
                            <button class="btn btn-primary me-2">
                                <i class="fa fa-filter"></i>
                            </button>
                            <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-rotate-left"></i>
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- Question Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Topic</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Negative</th>
                                <th>Marking</th>
                                <th>Status</th>
                                <th class="text-center" width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($questions as $index => $question)
                                <tr>
                                    <td>{{ $questions->firstItem() + $index }}</td>
                                    <td>{{ $question->course->name ?? '-' }}</td>
                                    <td>{{ $question->topic->title ?? '-' }}</td>

                                    <td style="max-width:300px">
                                        {{ Str::limit(strip_tags($question->question_text), 80) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-info text-dark">
                                            @switch($question->question_type)
                                                @case('single_choice')
                                                    Single
                                                @break

                                                @case('multiple_choice')
                                                    Multiple
                                                @break

                                                @case('text')
                                                    Text
                                                @break

                                                @case('file')
                                                    File
                                                @break
                                            @endswitch
                                        </span>
                                    </td>

                                    <td>{{ $question->max_marks }}</td>
                                    <td>{{ $question->negative_marks }}</td>

                                    <td>
                                        <span
                                            class="badge {{ $question->marking_type === 'automatic' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($question->marking_type) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge {{ $question->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('questions.edit', $question) }}"
                                            class="btn btn-sm btn-outline-warning">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <form action="{{ route('questions.destroy', $question) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this question?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            No questions found.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $questions->withQueryString()->links() }}
            </div>

        </div>

    @endsection
