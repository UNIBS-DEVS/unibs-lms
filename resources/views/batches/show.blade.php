@extends('layouts.app')

@section('title', 'View Batch | Unibs Tools')

@section('content')

    <div class="container-fluid mt-4 px-4">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Batch Details</h4>

                    @if (in_array(auth()->user()->role, ['admin', 'trainer']))
                        <div>
                            <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i>
                            </a>

                            <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                        </div>
                    @else
                        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    @endif
                </div>

                {{-- Card --}}
                <div class="card shadow-sm">
                    <div class="card-body">

                        {{-- Batch Name --}}
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Batch Name</div>
                            <div class="col-8">{{ $batch->name }}</div>
                        </div>

                        {{-- Customer --}}
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Customer</div>
                            <div class="col-8">
                                {{ $batch->customer?->name ?? '-' }}
                            </div>
                        </div>

                        {{-- Start Date --}}
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Start Date</div>
                            <div class="col-8">
                                {{ \Carbon\Carbon::parse($batch->start_date)->format('d M Y') }}
                            </div>
                        </div>

                        {{-- End Date --}}
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">End Date</div>
                            <div class="col-8">
                                {{ $batch->end_date ? \Carbon\Carbon::parse($batch->end_date)->format('d M Y') : '-' }}
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Status</div>
                            <div class="col-8">
                                <span class="badge {{ $batch->status === 'inactive' ? 'bg-danger' : 'bg-success' }}">
                                    {{ ucfirst($batch->status) }}
                                </span>
                            </div>
                        </div>

                        {{-- Created --}}
                        <div class="row">
                            <div class="col-4 fw-bold">Created</div>
                            <div class="col-8">
                                {{ $batch->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
