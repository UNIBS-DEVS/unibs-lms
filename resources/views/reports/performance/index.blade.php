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
            <h4 class="mb-0">Performance Report</h4>

            <div class="d-flex gap-2">
                <a href="{{ route('reports.performance.excel') }}" id="exportExcel" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

                <a href="{{ route('reports.performance.pdf') }}" id="exportPdf" class="btn btn-danger btn-sm">
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
                            <option value="">-- Select Batch --</option>

                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}">
                                    {{ $batch->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>

                </form>

            </div>
        </div>


        {{-- SUMMARY --}}
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Avg Attendance</h6>
                        <h3 class="fw-bold text-success" id="summary-attendance">0.00</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Avg Quiz</h6>
                        <h3 class="fw-bold text-primary" id="summary-quiz">0.00</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Avg Feedback</h6>
                        <h3 class="fw-bold text-info" id="summary-feedback">0.00</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Avg Score</h6>
                        <h3 class="fw-bold text-dark" id="summary-score">0.00</h3>
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
                            <th>Learner</th>
                            <th class="text-center">Attendance</th>
                            <th class="text-center">Quiz</th>
                            <th class="text-center">Feedback</th>
                            <th class="text-center">Avg Score</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Select batch to view performance.
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


            function loadPerformance() {
                let batch = $('select[name="batch_id"]').val();

                if (!batch) {
                    $('tbody').html(
                        '<tr><td colspan="7" class="text-center text-muted">Please select batch.</td></tr>'
                    );
                    return;
                }

                $.ajax({

                    url: "{{ route('reports.performance.filter') }}",
                    type: "GET",
                    data: $('#filterForm').serialize(),

                    success: function(res) {

                        /* UPDATE EXPORT BUTTON LINKS */
                        let params = $('#filterForm').serialize();

                        // alert(params);

                        $('#exportExcel').attr(
                            'href',
                            "{{ route('reports.performance.excel') }}?" + params
                        );

                        $('#exportPdf').attr(
                            'href',
                            "{{ route('reports.performance.pdf') }}?" + params
                        );

                        let rows = '';
                        let index = 1;

                        if (res.data.length) {

                            res.data.forEach(function(item) {

                                let statusClass = '';

                                if (item.status === 'Green')
                                    statusClass = 'bg-success';

                                else if (item.status === 'Amber')
                                    statusClass = 'bg-warning text-dark';

                                else
                                    statusClass = 'bg-danger';

                                rows += `
                        <tr>

                            <td>${index++}</td>
                            <td>${item.learner_name}</td>

                            <td class="text-center">
                                ${item.attendance.toFixed(2)}%
                            </td>

                            <td class="text-center">
                                ${item.quiz.toFixed(2)}%
                            </td>

                            <td class="text-center">
                                ${item.feedback.toFixed(2)}%
                            </td>

                            <td class="text-center fw-bold">
                                ${item.avg_score.toFixed(2)}%
                            </td>

                            <td class="text-center">
                                <span class="badge ${statusClass}">
                                    ${item.status}
                                </span>
                            </td>

                        </tr>`;
                            });

                        } else {
                            rows = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No records found
                        </td>
                    </tr>`;
                        }

                        $('tbody').html(rows);


                        /* SUMMARY */
                        // $('#summary-attendance').text(res.summary.attendance.toFixed(2));
                        // $('#summary-quiz').text(res.summary.quiz.toFixed(2));
                        // $('#summary-feedback').text(res.summary.feedback.toFixed(2));
                        // $('#summary-score').text(res.summary.avg_score.toFixed(2));

                        $('#summary-attendance').text(res.summary.attendance.toFixed(2) + '%');
                        $('#summary-quiz').text(res.summary.quiz.toFixed(2) + '%');
                        $('#summary-feedback').text(res.summary.feedback.toFixed(2) + '%');
                        $('#summary-score').text(res.summary.avg_score.toFixed(2) + '%');

                    },

                    error: function() {
                        $('tbody').html(
                            '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>'
                        );
                    }

                });

            }


            $('#filterForm').submit(function(e) {
                e.preventDefault();
                loadPerformance();
            });


            $('select[name="batch_id"]').change(loadPerformance);

            $('input[name="start_date"]').change(loadPerformance);

            $('input[name="end_date"]').change(loadPerformance);


        });
    </script>
@endpush
