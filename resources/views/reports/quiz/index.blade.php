@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- PAGE HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Quiz Report</h4>

            <div class="d-flex gap-2">
                <a id="exportExcelBtn" href="#" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

                <a id="exportPdfBtn" href="#" class="btn btn-danger btn-sm">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">

                <form id="filterForm" class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Batch</label>
                        <select name="batch_id" class="form-select select2">
                            <option value="">Select Batch</option>

                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}">
                                    {{ $batch->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Quiz</label>
                        <select name="quiz_id" id="quiz_id" class="form-select select2">
                            <option value="">Select Quiz</option>
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
                            <option value="completed_auto">Completed (Auto)</option>
                            <option value="pending_manual_review">Pending Manual</option>
                            <option value="result_published">Result Published</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-50">
                            <i class="fa fa-filter"></i> Filter
                        </button>

                        <button type="reset" class="btn btn-outline-secondary w-50">
                            <i class="fa fa-rotate-left"></i> Reset
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
                        <i class="fa fa-list text-primary fs-2"></i>
                        <h6 class="text-muted mt-2">Total Attempts</h6>
                        <h3 class="fw-bold summary-total">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-check-circle text-success fs-2"></i>
                        <h6 class="text-muted mt-2">Completed</h6>
                        <h3 class="fw-bold text-success summary-completed">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-clock text-warning fs-2"></i>
                        <h6 class="text-muted mt-2">Pending Manual</h6>
                        <h3 class="fw-bold text-warning summary-pending">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-chart-line text-info fs-2"></i>
                        <h6 class="text-muted mt-2">Average Score</h6>
                        <h3 class="fw-bold text-info summary-average">0</h3>
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
                            <th>Quiz</th>
                            <th>Learner</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Total</th>
                            <th>Percentage</th>
                            <th>Result</th>
                            <th>Completed At</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                Please select batch and quiz to view report.
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

            function loadQuizReport() {

                let batch = $('select[name="batch_id"]').val();
                let quiz = $('#quiz_id').val();

                if (!batch || !quiz) return;

                $.ajax({

                    url: "{{ route('reports.quiz.filter') }}",
                    type: "GET",
                    data: $('#filterForm').serialize(),

                    success: function(response) {

                        let rows = '';
                        let index = 1;

                        if (response.data.length) {

                            response.data.forEach(function(item) {

                                let badge = item.result === 'Pass' ?
                                    '<span class="badge bg-success">Pass</span>' :
                                    '<span class="badge bg-danger">Fail</span>';

                                rows += `<tr>

                        <td>${index++}</td>
                        <td>${item.quiz}</td>
                        <td>${item.learner}</td>
                        <td>${item.status}</td>
                        <td>${item.score}</td>
                        <td>${item.total}</td>
                        <td>${item.percentage ? item.percentage + '%' : '-'}</td>
                        <td>${badge}</td>
                        <td>${item.completed_at ?? '-'}</td>

                        </tr>`;
                            });

                        } else {

                            rows = `<tr>
                        <td colspan="9" class="text-center text-muted">
                        No quiz attempts found
                        </td>
                    </tr>`;
                        }

                        $('tbody').html(rows);

                        $('.summary-total').text(response.summary.total);
                        $('.summary-completed').text(response.summary.completed);
                        $('.summary-pending').text(response.summary.pending_manual);
                        $('.summary-average').text(response.summary.average_score);

                        let params = $('#filterForm').serialize();

                        $('#exportExcelBtn').attr('href', "{{ route('reports.quiz.excel') }}?" +
                            params);
                        $('#exportPdfBtn').attr('href', "{{ route('reports.quiz.pdf') }}?" + params);

                    }

                });

            }


            $('#filterForm').submit(function(e) {
                e.preventDefault();
                loadQuizReport();
            });

            $('#quiz_id').change(loadQuizReport);


            $('select[name="batch_id"]').change(function() {

                let batchId = $(this).val();

                $('#quiz_id').html('<option value="">Loading...</option>');

                if (!batchId) {
                    $('#quiz_id').html('<option value="">Select Quiz</option>');
                    return;
                }

                $.get("/reports/quiz/by-batch/" + batchId, function(quizzes) {

                    let options = '<option value="">Select Quiz</option>';

                    quizzes.forEach(function(q) {
                        options += `<option value="${q.id}">${q.title}</option>`;
                    });

                    $('#quiz_id').html(options);

                });

            });

        });
    </script>
@endpush
