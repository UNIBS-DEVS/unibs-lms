@extends('layouts.app')

@section('title', 'Default Feedback Questions | Unibs Tools')

{{-- @push('styles')
    <style>
        #feedbackTable thead th {
            background-color: #f8f9fa;
            vertical-align: middle;
            font-weight: 600;
            white-space: nowrap;
        }

        #feedbackTable .filter-row th {
            background-color: #ffffff;
            padding: 6px;
        }

        .column-search {
            border-radius: 6px;
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 20px;
        }
    </style>
@endpush --}}

@push('styles')
    <style>
        /* DataTables top controls wrapper */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }

        /* SEARCH BOX */
        .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .dataTables_wrapper .dataTables_filter label {
            position: relative;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            padding: 6px 12px 6px 36px;
            border: 1px solid #dee2e6;
            outline: none;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
        }

        .dataTables_wrapper .dataTables_filter label::before {
            content: "\f002";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: #6c757d;
        }

        /* SHOW ENTRIES */
        .dataTables_wrapper .dataTables_length {
            float: left;
        }

        .dataTables_wrapper .dataTables_length label {
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            padding: 4px 8px;
            border: 1px solid #dee2e6;
        }

        /* Clear floats */
        .dataTables_wrapper::after {
            content: "";
            display: block;
            clear: both;
        }
    </style>
@endpush


@section('content')
    <div class="container-fluid mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Default Feedback Questions</h4>

            <a href="{{ route('feedback.trainer.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white shadow-sm w-100" id="feedbackTable">

                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Question</th>
                        <th width="80" class="text-center">Type</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>

                    {{-- FILTER ROW --}}
                    <tr class="filter-row">
                        <th></th>

                        <th>
                            <input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search question">
                        </th>

                        <th>
                            <select class="form-select form-select-sm column-search">
                                <option value="">All</option>
                                <option value="trainer">Trainer</option>
                                <option value="learner">Learner</option>
                            </select>
                        </th>

                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($feedbacks as $fb)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $fb->question }}</td>
                            <td>
                                @php
                                    $types = [
                                        'trainer' => [
                                            'class' => 'bg-info',
                                            'icon' => 'fa-chalkboard-teacher',
                                            'label' => 'Trainer',
                                        ],
                                        'learner' => [
                                            'class' => 'bg-success',
                                            'icon' => 'fa-user-graduate',
                                            'label' => 'Learner',
                                        ],
                                    ];

                                    $type = $types[$fb->type] ?? [
                                        'class' => 'bg-secondary',
                                        'icon' => 'fa-question-circle',
                                        'label' => ucfirst($fb->type),
                                    ];
                                @endphp

                                <span class="badge {{ $type['class'] }} px-3 py-2">
                                    <i class="fa-solid {{ $type['icon'] }} me-1"></i>
                                    {{ $type['label'] }}
                                </span>
                            </td>


                            <td class="text-center">
                                <a href="{{ route('feedback.trainer.edit', $fb) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form action="{{ route('feedback.trainer.destroy', $fb) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this question?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            let table = $('#feedbackTable').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                pagingType: "simple_numbers",
                language: {
                    search: "",
                    searchPlaceholder: "Search feedback...",
                    lengthMenu: "Show _MENU_"
                },
                columnDefs: [{
                        orderable: false,
                        searchable: false,
                        targets: 3
                    } // Action column
                ]
            });

            // Question text filter
            $('#feedbackTable thead tr.filter-row th:eq(1) input')
                .on('keyup change', function() {
                    table.column(1).search(this.value).draw();
                });

            // Type dropdown filter
            $('#feedbackTable thead tr.filter-row th:eq(2) select')
                .on('change', function() {
                    table.column(2).search(this.value).draw();
                });
        });
    </script>
@endpush
