@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- PAGE TITLE --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Attendance Report</h4>

            <div class="d-flex gap-2">
                <a href="{{ route('reports.attendance.excel') }}" id="exportExcel" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

                <a href="{{ route('reports.attendance.pdf') }}" id="exportPdf" class="btn btn-danger btn-sm">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">

                <form id="filterForm" class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Batch</label>
                        <select name="batch_id" class="form-select select2">
                            <option value="">-- All Batches --</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}">
                                    {{ $batch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> 

                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control">
                    </div>    

                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50">
                            <i class="fa fa-filter"></i> Filter
                        </button>

                        <a href="{{ route('reports.attendance.index') }}" class="btn btn-outline-secondary w-50">
                            <i class="fa fa-rotate-left"></i> Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-user-check text-success fs-2"></i>
                        <h6 class="text-muted mt-2">Present</h6>
                        <h3 class="fw-bold text-success summary-present">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-user-times text-danger fs-2"></i>
                        <h6 class="text-muted mt-2">Absent</h6>
                        <h3 class="fw-bold text-danger summary-absent">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-clock text-warning fs-2"></i>
                        <h6 class="text-muted mt-2">Late Entry</h6>
                        <h3 class="fw-bold text-warning summary-late">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-door-open text-info fs-2"></i>
                        <h6 class="text-muted mt-2">Early Exit</h6>
                        <h3 class="fw-bold text-info summary-early">0</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Session</th>
                            <th>Session Date</th>
                            <th>Learner</th>
                            <th class="text-center">Attendance</th>
                            <th>Marked At</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Please select a batch to view attendance.
                            </td>
                        </tr>
                    </tbody>

                </table>

            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            $('.select2').select2({
                width: '100%'
            });

            function loadAttendance() {
                let batch = $('select[name="batch_id"]').val();

                if (!batch) {
                    $('tbody').html(`<tr>
            <td colspan="6" class="text-center text-muted">Please select a batch.</td>
            </tr>`);

                    $('.summary-present').text(0);
                    $('.summary-absent').text(0);
                    $('.summary-late').text(0);
                    $('.summary-early').text(0);

                    return;
                }

                $.ajax({

                    url: "{{ route('reports.attendance.filter') }}",
                    type: "GET",
                    data: $('#filterForm').serialize(),

                    success: function(response) {

                        /* UPDATE EXPORT BUTTON LINKS */
                        let params = $('#filterForm').serialize();

                        $('#exportExcel').attr(
                            'href',
                            "{{ route('reports.attendance.excel') }}?" + params
                        );

                        $('#exportPdf').attr(
                            'href',
                            "{{ route('reports.attendance.pdf') }}?" + params
                        );


                        let rows = '';
                        let index = 1;

                        if (response.data.length > 0) {
                            response.data.forEach(function(item) {

                                let badge = item.present === 'present' ?
                                    `<span class="badge bg-success">P</span>` :
                                    `<span class="badge bg-danger">A</span>`;

                                if (item.present === 'present') {
                                    if (item.late_entry === 'yes')
                                        badge +=
                                        ` <span class="badge bg-warning text-dark">LE</span>`;

                                    if (item.early_exit === 'yes')
                                        badge +=
                                        ` <span class="badge bg-info text-dark">EE</span>`;
                                }

                                rows += `<tr>

                        <td>${index++}</td>
                        <td>${item.session_name}</td>
                        <td>${item.session_date}</td>
                        <td>${item.learner_name}</td>
                        <td class="text-center">${badge}</td>
                        <td>${item.marked_at}</td>

                        </tr>`;
                            });
                        } else {
                            rows = `<tr>
                    <td colspan="6" class="text-center text-muted">
                    No attendance records found.
                    </td>
                    </tr>`;
                        }

                        $('tbody').html(rows);

                        $('.summary-present').text(response.summary.present);
                        $('.summary-absent').text(response.summary.absent);
                        $('.summary-late').text(response.summary.late_entry);
                        $('.summary-early').text(response.summary.early_exit);
                    }

                });
            }

            $('#filterForm').on('submit', function(e) {

                e.preventDefault();
                loadAttendance();

            });

            $('select[name="batch_id"]').on('change', loadAttendance);

        });
    </script>
@endpush
