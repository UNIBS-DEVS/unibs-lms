@extends('layouts.app')

@section('title', 'Batch Courses Details')

@section('content')

    <div class="container-fluid mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Batch Courses</h3>

            <div>
                <a href="{{ route('batch-courses.edit', $batch->id) }}" class="btn btn-warning">
                    <i class="fa fa-book"></i> Edit Courses
                </a>

                <a href="{{ route('batch-courses.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </div>
        </div>

        {{-- Batch Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="row mb-2">
                    <div class="col-md-3 fw-semibold">Batch Name</div>
                    <div class="col-md-9">{{ $batch->name }}</div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-semibold">Customer</div>
                    <div class="col-md-9">{{ $batch->customer?->name ?? '-' }}</div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-semibold">Status</div>
                    <div class="col-md-9">
                        @if ($batch->status === 'inactive')
                            <span class="badge bg-danger">Inactive</span>
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 fw-semibold">Total Courses</div>
                    <div class="col-md-9">
                        <span class="badge bg-info">{{ $batch->courses_count ?? 0 }}</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Courses List --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa fa-book me-1 text-primary"></i>
                    Courses in this Batch
                </h5>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">#</th>
                                <th>Course Name</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($batch->courses as $index => $course)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->category ?? '-' }}</td>
                                    <td>
                                        @if ($course->status === 'inactive')
                                            <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No courses assigned to this batch
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

@endsection
