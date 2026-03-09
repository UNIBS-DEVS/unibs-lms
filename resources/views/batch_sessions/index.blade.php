@extends('layouts.app')

@section('title', 'Batch Sessions | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
            Batch Sessions
        </h3>

        <a href="{{ route('batch-sessions.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white shadow-sm w-100" id="listTable">

            <thead>
                <tr>
                    <th>Session Name</th>
                    <th>Batch</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th width="140">Timing</th>
                    <th width="90" class="text-center">Type</th>
                    <th width="150" class="text-center">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Name', 'Batch', 'Start Date', 'End Date', 'Timing', 'Type'] as $placeholder)
                        <th>
                            <input type="text" class="form-control form-control-sm column-search"
                                placeholder="{{ $placeholder }}">
                        </th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($sessions as $session)
                    <tr>
                        <td class="fw-semibold">{{ $session->session_name }}</td>

                        <td>{{ $session->batch?->name ?? '-' }}</td>

                        <td>{{ optional($session->start_date)->format('d M Y') }}</td>

                        <td>{{ optional($session->end_date)->format('d M Y') }}</td>

                        <td>
                            {{ $session->start_time_formatted }} – {{ $session->end_time_formatted }}
                        </td>

                        <td class="text-center">
                            @php
                                $typeColors = [
                                    'online' => 'primary',
                                    'offline' => 'success',
                                ];
                            @endphp
                            <span class="badge bg-{{ $typeColors[$session->type] ?? 'secondary' }}">
                                <i class="fa fa-video me-1"></i>
                                {{ ucfirst($session->type) }}
                            </span>
                        </td>

                        <td class="text-center">

                            {{-- Attendance --}}
                            @if ($session->start_date->lte(now()->startOfDay()))
                                <a href="{{ route('sessions.attendance.index', $session->id) }}"
                                    class="btn btn-success btn-sm" title="Mark Attendance">
                                    <i class="fa fa-clipboard-check"></i>
                                </a>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled title="Attendance not allowed yet">
                                    <i class="fa fa-clock"></i>
                                </button>
                            @endif

                            <a href="{{ route('batch-sessions.show', $session->id) }}" class="btn btn-outline-info btn-sm"
                                title="View">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="{{ route('batch-sessions.edit', $session->id) }}"
                                class="btn btn-outline-warning btn-sm" title="Edit">
                                <i class="fa fa-pen"></i>
                            </a>

                            <form action="{{ route('batch-sessions.destroy', $session->id) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Delete this session?')">
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
