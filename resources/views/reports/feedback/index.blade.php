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

                <a href="{{ route('reports.feedback.excel') }}" id="exportExcel" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

                <a href="{{ route('reports.feedback.pdf') }}" id="exportPdf" class="btn btn-danger btn-sm">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>

            </div>
        </div>


        {{-- FILTERS --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">

                <form id="filterForm" class="row g-3 align-items-end">

                    <div class="col-md-4">
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


                    <div class="col-md-4">
                        <label class="form-label">Feedback Type</label>
                        <select name="type" class="form-select">
                            <option value="">All</option>
                            <option value="trainer">Trainer</option>
                            <option value="learner">Learner</option>
                        </select>
                    </div>


                    <div class="col-md-4 d-flex gap-2">

                        <button type="submit" class="btn btn-primary w-50">
                            <i class="fa fa-filter"></i> Filter
                        </button>

                        <a href="{{ route('reports.feedback.index') }}" class="btn btn-outline-secondary w-50">
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

                        <i class="fa fa-comments text-primary fs-2"></i>

                        <h6 class="text-muted mt-2">Total Feedback</h6>

                        <h3 class="fw-bold text-primary summary-total">0</h3>

                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">

                        <i class="fa fa-user text-success fs-2"></i>

                        <h6 class="text-muted mt-2">Trainer Feedback</h6>

                        <h3 class="fw-bold text-success summary-trainer">0</h3>

                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">

                        <i class="fa fa-graduation-cap text-warning fs-2"></i>

                        <h6 class="text-muted mt-2">Learner Feedback</h6>

                        <h3 class="fw-bold text-warning summary-learner">0</h3>

                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">

                        <i class="fa fa-chart-line text-info fs-2"></i>

                        <h6 class="text-muted mt-2">Average Score</h6>

                        <h3 class="fw-bold text-info summary-score">0</h3>

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
                            <th>Trainer</th>
                            <th>Categories</th>
                            <th class="text-center">Avg Score</th>
                            <th>Date</th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Please select filters to view feedback.
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


            function loadFeedback() {

                $.ajax({

                    url: "{{ route('reports.feedback.filter') }}",

                    type: "GET",

                    data: $('#filterForm').serialize(),

                    success: function(response) {

                        /* UPDATE EXPORT LINKS */

                        let params = $('#filterForm').serialize();

                        $('#exportExcel').attr(
                            'href',
                            "{{ route('reports.feedback.excel') }}?" + params
                        );

                        $('#exportPdf').attr(
                            'href',
                            "{{ route('reports.feedback.pdf') }}?" + params
                        );


                        /* BUILD TABLE */

                        let rows = '';
                        let index = 1;

                        if (response.data.length > 0) {

                            response.data.forEach(function(item) {

                                let cats = '';

                                item.categories.forEach(function(cat) {
                                    cats +=
                                        `<span class="badge bg-info">${cat}</span> `;
                                });

                                rows += `
                                        <tr>

                                        <td>${index++}</td>

                                        <td>${item.learner}</td>

                                        <td>${item.trainer}</td>

                                        <td>${cats}</td>

                                        <td class="text-center">
                                        <span class="badge bg-success">
                                        ${item.avg_score} / 5
                                        </span>
                                        </td>

                                        <td>${item.date}</td>

                                        </tr>
                                        `;

                            });

                        } else {

                            rows = `
<tr>
<td colspan="6" class="text-center text-muted">
No feedback records found.
</td>
</tr>
`;

                        }

                        $('tbody').html(rows);


                        /* SUMMARY */

                        $('.summary-total').text(response.summary.total);
                        $('.summary-trainer').text(response.summary.trainer);
                        $('.summary-learner').text(response.summary.learner);
                        $('.summary-score').text(response.summary.avg_score);

                    }

                });

            }



            $('#filterForm').on('submit', function(e) {

                e.preventDefault();

                loadFeedback();

            });



        });
    </script>
@endpush
