@extends('layouts.app')

@section('title', 'Batch Feedback Questions | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>
            Batch Feedback Questions
            <small class="text-muted">({{ $batch->name }})</small>
        </h3>

        <div class="d-flex gap-2">

            {{-- Load Default Questions --}}
            <form action="{{ route('batch-feedback-questions.load-default', $batch->id) }}" method="POST"
                onsubmit="return confirm('Load default feedback questions for this batch?')">
                @csrf
                <button class="btn btn-outline-secondary">
                    <i class="fa fa-download"></i>
                </button>
            </form>

            {{-- Add Question --}}
            <a href="{{ route('batch-feedback-questions.create', $batch->id) }}" class="btn btn-primary">
                <i class="fa fa-plus"></i>
            </a>

        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white w-100" id="listTable">

            <thead>
                <tr>
                    <th>Question</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th width="13%" class="text-center">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Question', 'Type', 'Category'] as $label)
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
                @foreach ($questions as $question)
                    <tr>

                        <td>{{ $question->question }}</td>

                        <td>
                            @if ($question->type === 'trainer')
                                <span class="badge bg-primary-subtle text-primary py-2">
                                    <i class="fa fa-chalkboard-teacher me-1"></i> Trainer
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success py-2">
                                    <i class="fa fa-user-graduate me-1"></i> Learner
                                </span>
                            @endif
                        </td>

                        <td> <span class="badge bg-info text-dark">{{ ucfirst($question->category) }}</span></td>

                        <td class="text-center">

                            <a href="{{ route('batch-feedback-questions.edit', [$batch->id, $question->id]) }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('batch-feedback-questions.destroy', [$batch->id, $question->id]) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Delete this question?')">
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
