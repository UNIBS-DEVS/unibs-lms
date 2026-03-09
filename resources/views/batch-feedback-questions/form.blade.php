@extends('layouts.app')

@section('title', isset($question) ? 'Edit Batch Feedback Question' : 'Add Batch Feedback Question')

@section('content')
    <div class="container mt-4">

        @include('partials.message')

        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-comment-dots text-primary me-2"></i>
                            {{ isset($question) ? 'Edit' : 'Add' }} Feedback Question
                            <small class="text-muted">({{ $batch->name }})</small>
                        </h5>
                    </div>

                    <div class="card-body">
                        <form method="POST"
                            action="{{ isset($question)
                                ? route('batch-feedback-questions.update', [$batch->id, $question->id])
                                : route('batch-feedback-questions.store', $batch->id) }}">

                            @csrf
                            @isset($question)
                                @method('PUT')
                            @endisset

                            {{-- Question --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Feedback Question <span class="text-danger">*</span>
                                </label>

                                <textarea name="question" class="form-control @error('question') is-invalid @enderror" rows="3"
                                    placeholder="Enter feedback question">{{ old('question', $question->question ?? '') }}</textarea>

                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Type --}}
                            <div class="mb-4 col-md-6">
                                <label class="form-label fw-semibold">
                                    Feedback Type <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-users"></i>
                                    </span>

                                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                                        <option value="">-- Select Type --</option>
                                        <option value="trainer"
                                            {{ old('type', $question->type ?? '') === 'trainer' ? 'selected' : '' }}>
                                            Trainer Feedback
                                        </option>
                                        <option value="learner"
                                            {{ old('type', $question->type ?? '') === 'learner' ? 'selected' : '' }}>
                                            Learner Feedback
                                        </option>
                                    </select>

                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('batch-feedback-questions.index', $batch->id) }}"
                                    class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>

                                <button class="btn btn-warning">
                                    <i class="fa fa-save me-1"></i>
                                    {{ isset($question) ? 'Update' : 'Save' }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
