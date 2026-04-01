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
            <h4 class="mb-0">Feedback Report</h4>

            <div class="d-flex gap-2">
                {{-- <a href="{{ route('reports.feedback.excel') }}" id="exportExcel" class="btn btn-success btn-sm"> --}}
                <a href="#" id="exportExcel" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

                {{-- <a href="{{ route('reports.feedback.pdf') }}" id="exportPdf" class="btn btn-danger btn-sm"> --}}

                <a href="#" id="exportPdf" class="btn btn-danger btn-sm">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">

                <form id="filterForm" class="row g-3">

                    <div class="col-md-4">
                        <label>Batch</label>
                        <select name="batch_id" class="form-select select2">
                            <option value="">Select</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Feedback Type</label>
                        <select name="type" class="form-select">

                            @if (auth()->user()->role === 'admin')
                                <option value="">Select</option>
                                <option value="trainer">Trainer</option>
                                <option value="learner">Learner</option>
                            @elseif(auth()->user()->role === 'trainer')
                                <option value="learner" selected>Learner</option>
                            @elseif(auth()->user()->role === 'learner')
                                <option value="trainer" selected>Trainer</option>
                            @endif

                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button class="btn btn-primary">Filter</button>
                        <button type="button" id="resetFilter" class="btn btn-secondary">Reset</button>
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
                        <h6 class="text-muted mt-2">Total Feedback</h6>
                        <h3 class="fw-bold text-primary summary-total">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-chalkboard-teacher text-success fs-2"></i>
                        <h6 class="text-muted mt-2">Trainer Feedback</h6>
                        <h3 class="fw-bold text-success summary-trainer">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-user-graduate text-info fs-2"></i>
                        <h6 class="text-muted mt-2">Learner Feedback</h6>
                        <h3 class="fw-bold text-info summary-learner">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="fa fa-star text-warning fs-2"></i>
                        <h6 class="text-muted mt-2">Average Score</h6>
                        <h3 class="fw-bold text-warning summary-avg">0</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE --}}
        <div class="card shadow-sm">

            <div class="card-body p-0">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>Learner</th>
                            <th>Trainer</th>
                            <th>Categories</th>
                            <th>Score</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody id="feedbackTable">

                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Please select Batch and Feedback Type
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="feedbackModal">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Feedback Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>Question</th>
                                <th width="120">Rating</th>
                            </tr>
                        </thead>

                        <tbody id="modalQuestions"></tbody>

                    </table>

                </div>

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

            function loadFeedback() {

                let batch = $('select[name=batch_id]').val();
                let type = $('select[name=type]').val();

                if (!batch || !type) {

                    $('#feedbackTable').html(`
                        <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                        Please select Batch and Feedback Type
                        </td>
                        </tr>
                        `);

                    return;
                }

                $.get("{{ route('reports.feedback.filter') }}",
                    $('#filterForm').serialize(),
                    function(response) {

                        $('.summary-total').text(response.summary.total);
                        $('.summary-trainer').text(response.summary.trainer);
                        $('.summary-learner').text(response.summary.learner);
                        $('.summary-avg').text(response.summary.avg_score);

                        let rows = '';
                        let index = 1;

                        if (response.data.length) {

                            response.data.forEach(function(item) {

                                let cats = '';

                                if (item.categories && item.categories.length > 0) {
                                    item.categories.forEach(function(cat) {
                                        cats +=
                                            `<span class="badge bg-primary me-1">${cat}</span>`;
                                    });
                                } else {
                                    cats = '-';
                                }

                                /* Convert table score to percentage */
                                let percent = Math.round((item.avg_score / 5) * 100);

                                rows += `
<tr>

<td>${index++}</td>

<td>${item.learner}</td>

<td>${item.trainer}</td>

<td>${cats}</td>

<td>
<span class="badge bg-success">${percent}%</span>
</td>

<td>${item.date}</td>

<td>
<a href="#" class="btn btn-sm btn-outline-primary view-feedback"
data-id="${item.id}">
View
</a>
</td>

</tr>
`;

                            });

                        } else {

                            rows = `
<tr>
<td colspan="7" class="text-center">
No records found
</td>
</tr>`;
                        }

                        $('#feedbackTable').html(rows);

                    });

            }

            $('#filterForm').submit(function(e) {
                e.preventDefault();
                loadFeedback();
            });


            $(document).on('click', '.view-feedback', function(e) {

                e.preventDefault();

                let id = $(this).data('id');

                $.get(`/reports/feedback/details/${id}`, function(data) {

                    let rows = '';

                    data.forEach(function(q) {

                        rows += `
<tr>
<td>${q.question}</td>
<td><span class="badge text-dark">${q.score} / 5</span></td>
</tr>
`;

                    });

                    $('#modalQuestions').html(rows);

                    let modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
                    modal.show();

                });

            });

        });

        $('#resetFilter').click(function() {

            $('#filterForm')[0].reset();
            $('.select2').val('').trigger('change');

            $('#feedbackTable').html(`
<tr>
<td colspan="7" class="text-center text-muted py-4">
Please select Batch and Feedback Type
</td>
</tr>
`);

            $('.summary-total').text(0);
            $('.summary-trainer').text(0);
            $('.summary-learner').text(0);
            $('.summary-avg').text(0);

        });

        $('#exportExcel').click(function(e) {

            e.preventDefault();

            let params = $('#filterForm').serialize();

            window.location = "{{ route('reports.feedback.excel') }}?" + params;

        });


        $('#exportPdf').click(function(e) {

            e.preventDefault();

            let params = $('#filterForm').serialize();

            window.location = "{{ route('reports.feedback.pdf') }}?" + params;

        });
    </script>
@endpush
