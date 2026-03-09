@extends('layouts.app')

@section('title', 'Edit Batch | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 px-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-layer-group me-2 text-primary"></i>
                            Edit Batch
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('batches.update', $batch->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">

                                {{-- Batch Name --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Batch Name *</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $batch->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status *</label>
                                    <select name="status" class="form-select">
                                        @foreach (['active', 'inactive'] as $status)
                                            <option value="{{ $status }}" @selected(old('status', $batch->status) === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Customer --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Customer <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-building"></i>
                                        </span>

                                        <div class="flex-grow-1">
                                            <select name="customer_id"
                                                class="form-select select2 @error('customer_id') is-invalid @enderror"
                                                data-placeholder="Search customer...">
                                                <option value=""></option>

                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" @selected(old('customer_id', $batch->customer_id) == $customer->id)>
                                                        {{ $customer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @error('customer_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Dates --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Start Date *</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ old('start_date', $batch->start_date) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ old('end_date', $batch->end_date) }}">
                                </div>

                                {{-- Percentage Settings --}}
                                <div class="col-12 mt-4">
                                    <h6 class="fw-semibold">
                                        <i class="fa fa-chart-line me-2"></i>
                                        Reports Settings
                                    </h6>
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Attendance %</label>
                                    <input type="number" name="attendance_percentage" class="form-control"
                                        value="{{ old('attendance_percentage', $batch->attendance_percentage) }}"
                                        min="1" max="100">
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Quiz %</label>
                                    <input type="number" name="quiz_percentage" class="form-control"
                                        value="{{ old('quiz_percentage', $batch->quiz_percentage) }}" min="1"
                                        max="100">
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Feedback %</label>
                                    <input type="number" name="feedback_percentage" class="form-control"
                                        value="{{ old('feedback_percentage', $batch->feedback_percentage) }}"
                                        min="1" max="100">
                                </div>

                                <div class="col-12">
                                    <small class="text-muted">
                                        Total: <strong><span id="percentage-total">100</span>%</strong>
                                    </small>

                                    <div id="percentage-warning" class="text-danger d-none">
                                        Attendance %, Quiz %, and Feedback % must total exactly 100.
                                    </div>
                                </div>

                                {{-- Performance Thresholds --}}
                                <div class="col-12 mt-4">
                                    <h6 class="fw-semibold">
                                        <i class="fa fa-gauge-high me-2"></i>
                                        Performance Thresholds
                                    </h6>
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Red %</label>
                                    <input type="number" name="red_percentage" class="form-control"
                                        value="{{ old('red_percentage', $batch->red_percentage) }}" min="1"
                                        max="100">
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Amber %</label>
                                    <input type="number" name="amber_percentage" class="form-control"
                                        value="{{ old('amber_percentage', $batch->amber_percentage) }}" min="1"
                                        max="100">
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Green %</label>
                                    <input type="number" name="green_percentage" class="form-control"
                                        value="{{ old('green_percentage', $batch->green_percentage) }}" min="1"
                                        max="100">
                                </div>

                                {{-- Attendance Settings --}}
                                <div class="col-12 mt-4">
                                    <h6 class="fw-semibold">
                                        <i class="fa fa-user-check me-2"></i>
                                        Attendance Settings
                                    </h6>
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Present Value</label>
                                    <input type="number" name="present_value" class="form-control"
                                        value="{{ old('present_value', $batch->present_value) }}" min="0">
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Late Entry Deduction</label>
                                    <input type="number" name="late_entry_value" class="form-control"
                                        value="{{ old('late_entry_value', $batch->late_entry_value) }}" step="0.5"
                                        min="0">
                                </div>

                                <div class="col-md-4 mt-1">
                                    <label class="form-label fw-semibold">Early Exit Deduction</label>
                                    <input type="number" name="early_exit_value" class="form-control"
                                        value="{{ old('early_exit_value', $batch->early_exit_value) }}" step="0.5"
                                        min="0">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa fa-pen me-1"></i> Update
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
        function validatePercentageTotal() {
            let attendance = parseInt($('input[name="attendance_percentage"]').val()) || 0;
            let quiz = parseInt($('input[name="quiz_percentage"]').val()) || 0;
            let feedback = parseInt($('input[name="feedback_percentage"]').val()) || 0;

            let total = attendance + quiz + feedback;

            $('#percentage-total').text(total);

            if (total !== 100) {
                $('#percentage-warning').removeClass('d-none');
                return false;
            } else {
                $('#percentage-warning').addClass('d-none');
                return true;
            }
        }

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true
            });

            $('input[name="attendance_percentage"], input[name="quiz_percentage"], input[name="feedback_percentage"]')
                .on('input', validatePercentageTotal);

            $('form').on('submit', function(e) {
                if (!validatePercentageTotal()) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
