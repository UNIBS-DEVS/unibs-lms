@extends('layouts.app')

@section('title', 'Batch TOC | Unibs Tools')

@section('content')
    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $batch->name }} TOC</h3>

        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            @if (in_array(auth()->user()->role, ['admin', 'trainer']))
                <a href="{{ route('batches.toc.create', $batch->id) }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white shadow-sm w-100" id="listTable">

            <thead>
                <tr>
                    <th>Title</th>
                    <th width="140">Planned Dates</th>
                    <th width="140">Course</th>
                    <th width="140" class="text-center">Trainer</th>
                    <th width="120">Remarks</th>
                    <th width="100" class="text-center">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Title', 'Planned', 'Course', 'Trainer', 'Remarks'] as $label)
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
                {{-- @forelse ($tocs as $item) --}}
                @foreach ($tocs as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->title }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->planned_start_date)->format('d M Y') }}<br>
                            <small class="text-muted">
                                to {{ \Carbon\Carbon::parse($item->)->format('d M Y') }}
                            </small>
                        </td>

                        <td>
                            @if ($item->actual_start_date)
                                {{ \Carbon\Carbon::parse($item->actual_start_date)->format('d M Y') }}<br>
                                <small class="text-muted">
                                    to
                                    {{ $item->actual_end_date ? \Carbon\Carbon::parse($item->actual_end_date)->format('d M Y') : '-' }}
                                </small>
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ $item->percentage }}%">
                                    {{ $item->percentage }}%
                                </div>
                            </div>
                        </td>

                        <td>
                            @php
                                $statusMap = [
                                    'planned' => ['secondary', 'calendar'],
                                    'in_progress' => ['primary', 'spinner'],
                                    'on_hold' => ['warning', 'pause-circle'],
                                    'completed' => ['success', 'check-circle'],
                                ];
                                [$color, $icon] = $statusMap[$item->status];
                            @endphp

                            <span class="badge bg-{{ $color }}">
                                <i class="fa fa-{{ $icon }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>

                        @if (in_array(auth()->user()->role, ['admin', 'trainer']))
                            <td class="text-center">
                                <a href="{{ route('batches.toc.edit', [$batch->id, $item->id]) }}"
                                    class="btn btn-outline-warning btn-sm">
                                    <i class="fa fa-pen"></i>
                                </a>

                                <form action="{{ route('batches.toc.destroy', [$batch->id, $item->id]) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Delete this TOC entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        @else
                            <td class="text-center">
                                <a href="{{ route('progress.index') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-chart-line"></i>
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
@endsection
