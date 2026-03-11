@extends('layouts.app')

@section('title', 'Edit Feedback Question | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 px-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-comment-dots me-2 text-primary"></i>
                            Edit Feedback Question
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('feedback.trainer.update', $feedback) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                {{-- Feedback Question --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Feedback Question <span class="text-danger">*</span>
                                    </label>

                                    <textarea name="question" class="form-control @error('question') is-invalid @enderror" rows="3"
                                        placeholder="Enter feedback question">{{ old('question', $feedback->question) }}</textarea>

                                    @error('question')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Feedback Type --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Feedback Type <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-users"></i>
                                        </span>

                                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                                            <option value="">-- Select Type --</option>
                                            <option value="trainer" @selected(old('type', $feedback->type) === 'trainer')>
                                                Trainer Feedback
                                            </option>
                                            <option value="learner" @selected(old('type', $feedback->type) === 'learner')>
                                                Learner Feedback
                                            </option>
                                        </select>

                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Feedback Type --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Feedback Category <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-users"></i>
                                        </span>

                                        <select name="category" class="form-select @error('category') is-invalid @enderror">
                                            <option value="">-- Select Category --</option>
                                            <option value="regular" @selected(old('category', $feedback->category) === 'regular')>
                                                Regular
                                            </option>
                                            <option value="viva" @selected(old('category', $feedback->category) === 'viva')>
                                                Viva
                                            </option>
                                            <option value="need based" @selected(old('category', $feedback->category) === 'need based')>
                                                Need Based
                                            </option>
                                        </select>

                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end mt-5 gap-2">
                                <a href="{{ route('feedback.trainer.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Update
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
