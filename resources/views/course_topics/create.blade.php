@extends('layouts.app')

@section('title', 'Add Topic | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 px-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-list me-2 text-primary"></i>
                            Add Topic
                            <small class="text-muted ms-2">
                                ({{ $course->name }})
                            </small>
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('courses.topics.store', $course->id) }}" method="POST">
                            @csrf

                            <div class="row g-4">

                                <!-- Title -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Title <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-heading"></i>
                                        </span>
                                        <input type="text" name="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            placeholder="Enter topic title" value="{{ old('title') }}">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Remark -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Remark
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-comment"></i>
                                        </span>
                                        <input type="text" name="remark"
                                            class="form-control @error('remark') is-invalid @enderror"
                                            placeholder="Enter remark" value="{{ old('remark') }}">
                                        @error('remark')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Description
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-align-left"></i>
                                        </span>
                                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Enter topic description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <a href="{{ route('courses.topics.index', $course->id) }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Create
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
