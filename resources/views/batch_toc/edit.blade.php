@extends('layouts.app')

@section('title', 'Edit Batch TOC | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 px-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-list-check me-2 text-primary"></i>
                            Edit TOC : {{ $batch->name }}
                        </h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('batches.toc.update', [$batch->id, $toc->id]) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row g-4">

                                {{-- Topic Title --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Topic Title <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-heading"></i>
                                        </span>
                                        <input type="text" name="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            placeholder="Enter Topic Title" value="{{ old('title', $toc->title) }}"
                                            required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Course --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Course <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-book"></i>
                                        </span>
                                        <select name="course_id"
                                            class="form-select @error('course_id') is-invalid @enderror" required>
                                            <option value="">-- Select Course --</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}" @selected(old('course_id', $toc->course_id) == $course->id)>
                                                    {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Trainer --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Trainer <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-user-tie"></i>
                                        </span>
                                        <select name="trainer_id"
                                            class="form-select @error('trainer_id') is-invalid @enderror" required>
                                            <option value="">-- Select Trainer --</option>
                                            @foreach ($trainers as $trainer)
                                                <option value="{{ $trainer->id }}" @selected(old('trainer_id', $toc->trainer_id) == $trainer->id)>
                                                    {{ $trainer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('trainer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Planned Start --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Planned Start Date <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" name="planned_start_date"
                                            class="form-control @error('planned_start_date') is-invalid @enderror"
                                            value="{{ old('planned_start_date', $toc->planned_start_date) }}" required>
                                        @error('planned_start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Planned End --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Planned End Date <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar-check"></i>
                                        </span>
                                        <input type="date" name="planned_end_date"
                                            class="form-control @error('planned_end_date') is-invalid @enderror"
                                            value="{{ old('planned_end_date', $toc->planned_end_date) }}" required>
                                        @error('planned_end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Admin Remarks --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Admin Remarks</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-comment-dots"></i>
                                        </span>
                                        <textarea name="remark_admin" rows="3" class="form-control @error('remark_admin') is-invalid @enderror"
                                            placeholder="Enter remarks (optional)">{{ old('remark_admin', $toc->remark_admin) }}</textarea>
                                        @error('remark_admin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <a href="{{ route('batches.toc.index', $batch->id) }}" class="btn btn-secondary">
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
