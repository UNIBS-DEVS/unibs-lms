@extends('layouts.app')

@section('title', 'Create Batch Session | Unibs Tools')

@section('content')

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa fa-calendar-days me-2 text-primary"></i>
                        Add Batch Session
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('batch-sessions.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">

                            <!-- Session Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Session Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-clipboard-list"></i>
                                    </span>
                                    <input type="text" name="session_name"
                                        class="form-control @error('session_name') is-invalid @enderror"
                                        placeholder="Session Name" value="{{ old('session_name') }}">
                                    @error('session_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Batch <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-layer-group"></i>
                                    </span>

                                    <select name="batch_id" id="batch_id"
                                        class="form-select select2 @error('batch_id') is-invalid @enderror"
                                        data-placeholder="Search batch...">
                                        <option value="">-- Select Batch --</option>
                                        @foreach ($batches as $batch)
                                            <option value="{{ $batch->id }}" @selected(old('batch_id') == $batch->id)>
                                                {{ $batch->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('batch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Trainer <span class="text-danger">*</span>
                                </label>
                                <select name="trainer_id" id="trainer_id"
                                    class="form-select @error('trainer_id') is-invalid @enderror">
                                    <option value="">-- Select Trainer --</option>
                                </select>
                                @error('trainer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Course <span class="text-danger">*</span>
                                </label>
                                <select name="course_id" id="course_id"
                                    class="form-select @error('course_id') is-invalid @enderror">
                                    <option value="">-- Select Course --</option>
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Start Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input type="date" name="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Start Time -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-clock"></i>
                                    </span>
                                    <input type="time" name="start_time"
                                        class="form-control @error('start_time') is-invalid @enderror"
                                        value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    End Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-calendar-check"></i>
                                    </span>
                                    <input type="date" name="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Time -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-clock-rotate-left"></i>
                                    </span>
                                    <input type="time" name="end_time"
                                        class="form-control @error('end_time') is-invalid @enderror"
                                        value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Location
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-location-dot"></i>
                                    </span>
                                    <input type="text" name="location"
                                        class="form-control @error('location') is-invalid @enderror"
                                        placeholder="Location" value="{{ old('location') }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Session Type <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-video"></i>
                                    </span>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                                        <option value="Online" @selected(old('type') === 'Online')>Online</option>
                                        <option value="Offline" @selected(old('type') === 'Offline')>Offline</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-end mt-3 gap-2">
                                <a href="{{ route('batch-sessions.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Create
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#batch_id').on('change', function() {

                let batchId = $(this).val();
                let trainerDropdown = $('#trainer_id');
                let courseDropdown = $('#course_id');

                // Reset dropdowns
                trainerDropdown.html('<option value="">Loading...</option>');
                courseDropdown.html('<option value="">Loading...</option>');

                if (batchId) {

                    $.ajax({
                        url: "{{ url('/batches') }}/" + batchId + "/details",
                        type: "GET",
                        dataType: "json",
                        success: function(data) {

                            // Trainers
                            trainerDropdown.html(
                                '<option value="">-- Select Trainer --</option>');
                            $.each(data.trainers, function(key, trainer) {
                                trainerDropdown.append(
                                    '<option value="' + trainer.id + '">' + trainer
                                    .name + '</option>'
                                );
                            });

                            // Courses
                            courseDropdown.html(
                                '<option value="">-- Select Course --</option>');
                            $.each(data.courses, function(key, course) {
                                courseDropdown.append(
                                    '<option value="' + course.id + '">' + course
                                    .name + '</option>'
                                );
                            });

                            // Restore old values after validation error
                            let oldTrainer = "{{ old('trainer_id') }}";
                            let oldCourse = "{{ old('course_id') }}";

                            if (oldTrainer) {
                                trainerDropdown.val(oldTrainer);
                            }

                            if (oldCourse) {
                                courseDropdown.val(oldCourse);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            trainerDropdown.html(
                                '<option value="">Error loading trainers</option>');
                            courseDropdown.html(
                                '<option value="">Error loading courses</option>');
                        }
                    });

                } else {
                    trainerDropdown.html('<option value="">-- Select Trainer --</option>');
                    courseDropdown.html('<option value="">-- Select Course --</option>');
                }

            });

        });
    </script>
@endpush
