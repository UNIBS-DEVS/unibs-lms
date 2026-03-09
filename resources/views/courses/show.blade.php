@extends('layouts.app')

@section('title', 'View Course | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 px-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Course Details</h4>
                    <div>
                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>

                        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                </div>

                {{-- Card --}}
                <div class="card shadow-sm">
                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Name</div>
                            <div class="col-8">{{ $course->name }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Study Path</div>
                            <div class="col-8">{{ $course->study_material_path }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Category</div>
                            <div class="col-8">
                                <span class="badge bg-info">
                                    {{ ucfirst($course->category) }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Status</div>
                            <div class="col-8">
                                <span class="badge {{ $course->status === 'inactive' ? 'bg-danger' : 'bg-success' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4 fw-bold">Created</div>
                            <div class="col-8">
                                {{ $course->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
