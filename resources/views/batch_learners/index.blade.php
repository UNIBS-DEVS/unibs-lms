@extends('layouts.app')

@section('title', 'Batch Learners | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="fa-solid fa-users me-2 text-primary"></i>
            Batch Learners
        </h3>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white shadow-sm w-100" id="listTable">

            <thead>
                <tr>
                    <th>Batch Name</th>
                    {{-- <th>Course</th> --}}
                    <th>Customer</th>
                    <th>Learners</th>
                    <th width="110">Status</th>
                    <th width="110">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Batch name', 'Customer', 'Learners', 'Status'] as $placeholder)
                        <th>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control column-search" placeholder="{{ $placeholder }}">
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
                @forelse ($batches as $batch)
                    <tr>
                        <td class="fw-semibold">{{ $batch->name }}</td>

                        {{-- <td>{{ $batch->course?->name ?? '-' }}</td> --}}

                        <td class="text-center">
                            {{ $batch->customer?->name ?? '-' }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info">
                                <i class="fa fa-user-graduate me-1"></i>
                                {{ $batch->learners_count }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if ($batch->status === 'inactive')
                                <span class="badge bg-danger-subtle text-danger">
                                    <i class="fa fa-circle-xmark me-1"></i> Inactive
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success">
                                    <i class="fa fa-circle-check me-1"></i> Active
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <a href="{{ route('batch-learners.show', $batch->id) }}" class="btn btn-outline-info btn-sm"
                                title="View Learners">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="{{ route('batch-learners.edit', $batch->id) }}" class="btn btn-outline-warning btn-sm"
                                title="Assign Learners">
                                <i class="fa fa-users"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No batches found
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
@endsection
