@extends('layouts.app')

@section('title', 'Create Course | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 px-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-book me-2 text-primary"></i>
                            Add Course
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('courses.store') }}" method="POST">
                            @csrf

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Course Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-book"></i>
                                        </span>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Course Name" value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Study Material Path <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-book"></i>
                                        </span>
                                        <input type="text" name="study_material_path"
                                            class="form-control @error('study_material_path') is-invalid @enderror"
                                            placeholder="Study Material Path" value="{{ old('study_material_path') }}">
                                        @error('study_material_path')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Category <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-toggle-on"></i>
                                        </span>

                                        <!-- IMPORTANT wrapper for Select2 inside input-group -->
                                        <div class="flex-grow-1">
                                            <select name="category"
                                                class="form-select select2 @error('category') is-invalid @enderror"
                                                data-placeholder="Search category...">
                                                <option value=""></option>

                                                @foreach (['technical', 'managerial', 'functional', 'soft skill', 'others'] as $category)
                                                    <option value="{{ $category }}" @selected(old('category') === $category)>
                                                        {{ ucfirst($category) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @error('category')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-toggle-on"></i>
                                        </span>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">-- None --</option>
                                            @foreach (['active', 'inactive'] as $status)
                                                <option value="{{ $status }}" @selected(old('status') === $status)>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-end mt-3 gap-2">
                                    <a href="{{ route('courses.index') }}" class="btn btn-secondary">
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true,
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });
        });
    </script>
@endpush
