@extends('layouts.app')

@section('title', 'Batch Progress | Unibs Tools')

@section('content')

    @include('partials.message')

    <h3 class="mb-4">Batch Progress</h3>

    @if ($batches->count())

        {{-- Batch Dropdown --}}
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
                            <th width="100">Action</th>
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

            $('#batchDropdown').on('change', function() {

                let batchId = $(this).val();
                let tbody = $('#tocTableBody');
                tbody.empty();

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

                if (selectedBatch.tocs.length === 0) {
                    tbody.html(`
                    <tr>
                        <td colspan="6" class="text-center">
                            No TOC entries found.
                        </td>
                    </tr>
                `);
                    return;
                }

                selectedBatch.tocs.forEach(function(toc) {

                    let status = toc.status.replace('_', ' ');
                    status = status.charAt(0).toUpperCase() + status.slice(1);

                    tbody.append(`
                    <tr>
                        <td class="text-center">${toc.title}</td>
                        <td class="text-center">${toc.plan_start_date}</td>
                        <td class="text-center">${toc.plan_end_date}</td>
                        <td class="text-center"><span class="badge bg-info">${status}</span></td>
                        <td class="text-center">${toc.percentage ?? 0}%</td>
                        <td class="text-center">
                            <a href="/progress/${batchId}/${toc.id}/edit"
                               class="btn btn-sm btn-warning">
                                <i class="fa fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                `);
                });

            });

        });
    </script>
@endpush
