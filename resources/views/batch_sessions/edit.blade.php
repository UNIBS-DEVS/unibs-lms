@extends('layouts.app')

@section('title', 'Edit Batch Session | Unibs Tools')

@section('content')

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa fa-calendar-days me-2 text-primary"></i>
                        Edit Batch Session
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('batch-sessions.update', $session->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">

                            <!-- Session Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Session Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="session_name"
                                    class="form-control @error('session_name') is-invalid @enderror"
                                    value="{{ old('session_name', $session->session_name) }}">
                                @error('session_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Batch -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Batch <span class="text-danger">*</span>
                                </label>
                                <select name="batch_id" id="batch_id"
                                    class="form-select select2 @error('batch_id') is-invalid @enderror"
                                    data-placeholder="Search batch...">

                                    <option value="">-- Select Batch --</option>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}" @selected(old('batch_id', $session->batch_id) == $batch->id)>
                                            {{ $batch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('batch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Trainer -->
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

                            <!-- Course -->
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
                                <input type="date" name="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', optional($session->start_date)->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Time -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="start_time"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($session->start_time)->format('H:i')) }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    End Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="end_date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date', optional($session->end_date)->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="end_time"
                                    class="form-control @error('end_time') is-invalid @enderror"
                                    value="{{ old('end_time', \Carbon\Carbon::parse($session->end_time)->format('H:i')) }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Location</label>
                                <input type="text" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location', $session->location) }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Session Type <span class="text-danger">*</span>
                                </label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="Online" @selected(old('type', $session->type) === 'Online')>
                                        Online
                                    </option>
                                    <option value="Offline" @selected(old('type', $session->type) === 'Offline')>
                                        Offline
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-end mt-5 gap-2">
                                <a href="{{ route('batch-sessions.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i>
                                </a>

                                <button type="submit" class="btn btn-warning">
                                    <i class="fa fa-save me-1"></i> Update
                                </button>
                            </div>

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

            function loadBatchDetails(batchId, selectedTrainer = null, selectedCourse = null) {

                let trainerDropdown = $('#trainer_id');
                let courseDropdown = $('#course_id');

                trainerDropdown.html('<option value="">Loading...</option>');
                courseDropdown.html('<option value="">Loading...</option>');

                $.ajax({
                    url: "{{ url('/batches') }}/" + batchId + "/details",
                    type: "GET",
                    dataType: "json",
                    success: function(data) {

                        trainerDropdown.html('<option value="">-- Select Trainer --</option>');
                        $.each(data.trainers, function(key, trainer) {
                            trainerDropdown.append(
                                '<option value="' + trainer.id + '">' + trainer.name +
                                '</option>'
                            );
                        });

                        courseDropdown.html('<option value="">-- Select Course --</option>');
                        $.each(data.courses, function(key, course) {
                            courseDropdown.append(
                                '<option value="' + course.id + '">' + course.name +
                                '</option>'
                            );
                        });

                        if (selectedTrainer) {
                            trainerDropdown.val(selectedTrainer);
                        }

                        if (selectedCourse) {
                            courseDropdown.val(selectedCourse);
                        }
                    }
                });
            }

            $('#batch_id').on('change', function() {
                let batchId = $(this).val();
                if (batchId) {
                    loadBatchDetails(batchId);
                }
            });

            // 🔥 Auto load on edit page
            let initialBatch = "{{ old('batch_id', $session->batch_id) }}";
            let initialTrainer = "{{ old('trainer_id', $session->trainer_id) }}";
            let initialCourse = "{{ old('course_id', $session->course_id) }}";

            if (initialBatch) {
                loadBatchDetails(initialBatch, initialTrainer, initialCourse);
            }

        });
    </script>
@endpush
