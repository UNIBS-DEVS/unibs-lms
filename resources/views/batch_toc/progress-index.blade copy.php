@extends('layouts.app')

@section('title', 'Batch Progress | Unibs Tools')

@section('content')

    @include('partials.message')

    <h3 class="mb-4">Batch Progress</h3>

    @if ($batches->count())

        {{-- Batch Dropdown --}}
        @if (auth()->user()->role !== 'learner')
            <div class="mb-4" style="max-width:300px;">
                <label class="form-label fw-semibold">Select Batch</label>
                <select id="batchDropdown" class="form-select">
                    <option value="">-- Select Batch --</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->id }}">
                            {{ $batch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <script>
            let batchData = @json($batches);
        </script>

        {{-- Single Table --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Planned Start</th>
                            <th>Planned End</th>
                            <th>Status</th>
                            <th width="120">Progress</th>

                            @if (auth()->user()->role === 'learner')
                            @else
                                <th width="100">Action</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody id="tocTableBody">
                        <tr>
                            <td colspan="6" class="text-center">
                                Please select a batch.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            No batches assigned.
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            let isLearner = "{{ auth()->user()->role }}" === 'learner';
            let tbody = $('#tocTableBody');

            // ✅ AUTO LOAD FOR LEARNER
            if (isLearner) {

                if (batchData.length === 0) {
                    tbody.html(`
                <tr>
                    <td colspan="6" class="text-center">No batches assigned.</td>
                </tr>
            `);
                    return;
                }

                // 👉 If learner has multiple batches → show ALL TOCs
                let allTocs = [];

                batchData.forEach(batch => {
                    batch.tocs.forEach(toc => {
                        allTocs.push({
                            ...toc,
                            batch_id: batch.id
                        });
                    });
                });

                renderTable(allTocs);
            }

            $('#batchDropdown').on('change', function() {

                    let batchId = $(this).val();

                    // let tbody = $('#tocTableBody');
                    // tbody.empty();

                    if (!batchId) {
                        tbody.html(`
                    <tr>
                        <td colspan="6" class="text-center">
                            Please select a batch.
                        </td>
                    </tr>
                `);
                        return;
                    }

                    let selectedBatch = batchData.find(b => b.id == batchId);

                    renderTable(selectedBatch.tocs);
                    if (selectedBatch.tocs.length === 0) {
                        tbody.html(`
                                <tr>
                                    <td colspan="6" class="text-center">
                                        No TOC entries found.
                                    </td>
                                </tr>
                            `);
                        return;
                    });

                selectedBatch.tocs.forEach(function(toc) {

                    let status = toc.status.replace('_', ' ');
                    status = status.charAt(0).toUpperCase() + status.slice(1);


                    let isLearner = "{{ auth()->user()->role }}" === 'learner';
                    tbody.append(`
                    <tr>
                        <td class="text-center">${toc.title}</td>
                        <td class="text-center">${formatDate(toc.planned_start_date)}</td>
                        <td class="text-center">${formatDate(toc.planned_end_date)}</td>
                        <td class="text-center"><span class="badge bg-info">${status}</span></td>
                        <td class="text-center">${toc.percentage ?? 0}%</td>
                       
                        ${!isLearner ? `<td class="text-center">
                                                                                                                                                            <a href="/progress/${batchId}/${toc.id}/edit"
                                                                                                                                                                class="btn btn-sm btn-warning">
                                                                                                                                                                <i class="fa fa-pen"></i>
                                                                                                                                                            </a>
                                                                                                                                                        ` : `
                                                                                                                                                        </td>
                                                                                                                                                    `} 
                    </tr>
                `);
                });

            });

        function formatDate(dateString) {
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('en-GB', options);
        }
        });


        function renderTable(tocs) {

            let tbody = $('#tocTableBody');
            tbody.empty();

            if (!tocs || tocs.length === 0) {
                tbody.html(`
        <tr>
            <td colspan="6" class="text-center">No TOC entries found.</td>
        </tr>
    `);
                return;
            }

            let isLearner = "{{ auth()->user()->role }}" === 'learner';

            tocs.forEach(function(toc) {

                let status = toc.status.replace('_', ' ');
                status = status.charAt(0).toUpperCase() + status.slice(1);

                tbody.append(`
        <tr>
            <td class="text-center">${toc.title}</td>
            <td class="text-center">${formatDate(toc.planned_start_date)}</td>
            <td class="text-center">${formatDate(toc.planned_end_date)}</td>
            <td class="text-center"><span class="badge bg-info">${status}</span></td>
            <td class="text-center">${toc.percentage ?? 0}%</td>

            ${!isLearner ? `
                                                            <td class="text-center">
                                                                <a href="/progress/${toc.batch_id ?? ''}/${toc.id}/edit"
                                                                    class="btn btn-sm btn-warning">
                                                                    <i class="fa fa-pen"></i>
                                                                </a>
                                                            </td>
                                                        ` : ``}
        </tr>
    `);
            });
        }
    </script>
@endpush
