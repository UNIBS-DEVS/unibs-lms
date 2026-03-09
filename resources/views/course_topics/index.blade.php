@extends('layouts.app')

@section('title', 'Course Topics | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="mb-0">{{ $course->name }}</h3>

        <div class="d-flex gap-2">
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <a href="{{ route('courses.topics.create', $course->id) }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>

    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white w-100" id="listTable">

            <thead>
                <tr>
                    <th width="180">Title</th>
                    <th>Description</th>
                    <th>Remarks</th>
                    <th width="100" class="text-center">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Title', 'Description', 'Remarks'] as $label)
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
                @foreach ($topics as $topic)
                    <tr>
                        <td class="fw-semibold">{{ $topic->title }}</td>
                        <td>{{ $topic->description }}</td>
                        <td>{{ $topic->remark }}</td>

                        <td class="text-center">
                            <a href="{{ route('courses.topics.edit', [$course->id, $topic->id]) }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('courses.topics.destroy', [$course->id, $topic->id]) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this topic?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
