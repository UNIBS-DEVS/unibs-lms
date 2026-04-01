@extends('layouts.app')

@section('title', 'Batch TOC | Unibs Tools')

@section('content')
    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $batch->name }} TOC</h3>

        <div class="d-flex gap-2"> 

            <a href="{{ route('batches.index') }}" class="btn btn-secondary">
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
                    @if (in_array(auth()->user()->role, ['admin', 'trainer']))
                        <th width="100" class="text-center">Actions</th>
                    @endif
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
                    @if (in_array(auth()->user()->role, ['admin', 'trainer']))
                        <th></th>
                    @endif
                </tr>
            </thead>

            <tbody>
                {{-- @forelse ($tocs as $item) --}}
                @foreach ($tocs as $item)
                    <tr>
                        {{-- Title --}}
                        <td class="fw-semibold">{{ $item->title }}</td>

                        {{-- Planned Dates --}}
                        <td>
                            {{ \Carbon\Carbon::parse($item->planned_start_date)->format('d M Y') }}<br>
                            <small class="text-muted">
                                to {{ \Carbon\Carbon::parse($item->planned_end_date)->format('d M Y') }}
                            </small>
                        </td>

                        {{-- Course --}}
                        <td>
                            {{ $item->course->name ?? '-' }}
                        </td>

                        {{-- Trainer --}}
                        <td class="text-center">
                            {{ $item->trainer->name ?? '-' }}
                        </td>

                        {{-- Remarks --}}
                        <td>
                            {{ $item->remark_admin ?? '-' }}
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
                        @endif
                @endforeach

            </tbody>
        </table>
    </div>
@endsection
