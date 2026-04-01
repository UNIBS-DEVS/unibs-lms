@extends('layouts.app')

@section('title', 'Dashboard | Unibs Tools')

@section('content')

    {{-- ✅ LEARNER VIEW --}}
    @if ($user->role === 'learner')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">My Batches</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white shadow-sm w-100" id="listTable">

                <thead>
                    <tr>
                        <th>Batch Name</th>
                        <th>Customer</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th width="13%">Status</th>
                        <th width="21%">Actions</th>
                    </tr>

                    <tr class="filter-row">
                        @foreach (['Batch name', 'Customer', 'Start date', 'End date', 'Status'] as $label)
                            <th>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control column-search"
                                        placeholder="{{ $label }}">
                                    <span class="input-group-text clear-input">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                            </th>
                        @endforeach
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($batches as $batch)
                        <tr>
                            <td class="fw-semibold">{{ $batch->name }}</td>
                            <td>{{ $batch->customer?->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($batch->start_date)->format('d M Y') }}</td>
                            <td>
                                {{ $batch->end_date ? \Carbon\Carbon::parse($batch->end_date)->format('d M Y') : '-' }}
                            </td>
                            <td>
                                @if ($batch->status === 'inactive')
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="fa fa-ban me-1"></i> Inactive
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="fa fa-check-circle me-1"></i> Active
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('batches.toc.index', $batch->id) }}"
                                    class="btn btn-outline-secondary btn-sm" title="Batch TOC">
                                    <i class="fa fa-list"></i>
                                </a>

                                <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-outline-info btn-sm"
                                    title="View">
                                    <i class="fa fa-eye"></i>
                                </a>

                                <a href="{{ route('progress.index', $batch->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-chart-line"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        {{-- ✅ ADMIN / TRAINER VIEW --}}
    @else
        <div class="row g-4">

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h4 class="card-title">Dashboard</h4>
                        <p class="card-text">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda, aliquid beatae.
                            Accusamus
                            totam eos asperiores, non tempora quaerat reiciendis quidem ea illo laudantium. Quia
                            mollitia,
                            libero dolores maiores nesciunt consectetur!
                        </p>
                        <a href="#" class="btn btn-primary">
                            Go to Demo
                        </a>
                    </div>
                </div>
            </div>

        </div>

    @endif

    {{-- </div> --}}

@endsection
