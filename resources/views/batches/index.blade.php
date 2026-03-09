@extends('layouts.app')

@section('title', 'Batch List | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Batches</h3>

        <a href="{{ route('batches.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
        </a>
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
                                <input type="text" class="form-control column-search" placeholder="{{ $label }}">
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
                            <a href="{{ route('batches.toc.index', $batch->id) }}" class="btn btn-outline-secondary btn-sm"
                                title="Batch TOC">
                                <i class="fa fa-list"></i>
                            </a>

                            @if (in_array(auth()->user()->role, ['admin', 'trainer']))
                                <a href="{{ route('batch-feedback-questions.index', $batch->id) }}"
                                    class="btn btn-outline-primary btn-sm" title="Feedback Questions">
                                    <i class="fa fa-comment-dots"></i>
                                </a>
                            @endif

                            <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-outline-info btn-sm"
                                title="View">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-outline-warning btn-sm"
                                title="Edit">
                                <i class="fa fa-pen"></i>
                            </a>

                            <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Delete this batch?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
@endsection
