@extends('layouts.app')

@section('title', 'Add Feedback Question | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 px-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-comment-dots me-2 text-primary"></i>
                            Add Feedback Question
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('feedback.trainer.store') }}" method="POST">
                            @csrf

                            <div class="row g-4">
                                <div>
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
                                            <option value="trainer" @selected(old('type') === 'trainer')>
                                                Trainer Feedback
                                            </option>
                                            <option value="learner" @selected(old('type') === 'learner')>
                                                Learner Feedback
                                            </option>
                                        </select>

                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Category --}}
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
                                            <option value="regular" @selected(old('category') === 'regular')>
                                                Regular
                                            </option>

                                            <option value="viva" @selected(old('category') === 'viva')>
                                                Viva
                                            </option>

                                            <option value="need based" @selected(old('category') === 'need based')>
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
                                    <i class="fa fa-save me-1"></i> Save
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
