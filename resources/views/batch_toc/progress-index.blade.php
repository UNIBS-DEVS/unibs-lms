@extends('layouts.app')

@section('title', 'Batch Progress | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Batch Progress</h3>

        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>

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

        {{-- Table --}}
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

                            @if (auth()->user()->role !== 'learner')
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

            // ✅ DROPDOWN CHANGE (Trainer/Admin)
            $('#batchDropdown').on('change', function() {

                let batchId = $(this).val();

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

                if (!selectedBatch || selectedBatch.tocs.length === 0) {
                    tbody.html(`
                <tr>
                    <td colspan="6" class="text-center">
                        No TOC entries found.
                    </td>
                </tr>
            `);
                    return;
                }

                let tocsWithBatch = selectedBatch.tocs.map(toc => ({
                    ...toc,
                    batch_id: batchId
                }));

                renderTable(tocsWithBatch);
            });

        });

        // ✅ RENDER FUNCTION (GLOBAL)
        function renderTable(tocs) {

            let tbody = $('#tocTableBody');
            tbody.empty();

            let isLearner = "{{ auth()->user()->role }}" === 'learner';

            if (!tocs || tocs.length === 0) {
                tbody.html(`
            <tr>
                <td colspan="6" class="text-center">No TOC entries found.</td>
            </tr>
        `);
                return;
            }

            tocs.forEach(function(toc) {

                let status = toc.status.replace('_', ' ');
                status = status.charAt(0).toUpperCase() + status.slice(1);

                tbody.append(`
            <tr>
                <td class="text-center">${toc.title}</td>
                <td class="text-center">${formatDate(toc.planned_start_date)}</td>
                <td class="text-center">${formatDate(toc.planned_end_date)}</td>
                <td class="text-center">
                    <span class="badge bg-info">${status}</span>
                </td>
                <td class="text-center">${toc.percentage ?? 0}%</td>

                ${!isLearner ? `
                                        <td class="text-center">
                                            <a href="/progress/${toc.batch_id}/${toc.id}/edit"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        </td>
                                    ` : ``}
            </tr>
        `);
            });
        }

        // ✅ DATE FORMAT
        function formatDate(dateString) {
            if (!dateString) return '-';

            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };

            return new Date(dateString).toLocaleDateString('en-GB', options);
        }
    </script>
@endpush
