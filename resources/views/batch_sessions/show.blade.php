@extends('layouts.app')

@section('title', 'Batch Session Details')

@section('content')

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Batch Session</h3>

                <div>
                    <a href="{{ route('batch-sessions.edit', $session->id) }}" class="btn btn-warning">
                        <i class="fa fa-pen"></i>
                    </a>

                    <a href="{{ route('batch-sessions.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </div>
            </div>

            {{-- Session Info --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Session Name</div>
                        <div class="col-md-9">{{ $session->session_name }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Batch</div>
                        <div class="col-md-9">{{ $session->batch?->name ?? '-' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Course</div>
                        <div class="col-md-9">{{ $session->course?->name ?? '-' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Trainer</div>
                        <div class="col-md-9">
                            {{ $session->trainer?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Start Date</div>
                        <div class="col-md-9">
                            {{ optional($session->start_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">End Date</div>
                        <div class="col-md-9">
                            {{ optional($session->end_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Timing </div>
                        <div class="col-md-9">
                            {{ $session->start_time_formatted }} - {{ $session->end_time_formatted }}

                            {{-- {{ $session->start_time }} - {{ $session->end_time }} --}}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Location</div>
                        <div class="col-md-9">
                            {{ $session->location ?? '-' }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 fw-semibold">Session Type</div>
                        <div class="col-md-9">
                            @if ($session->type === 'Offline')
                                <span class="badge bg-warning-subtle text-warning">Offline</span>
                            @else
                                <span class="badge bg-info-subtle text-info">Online</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

@endsection
