@extends('layouts.app')

@section('title', 'Course List | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Course</h3>

        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white w-100" id="listTable">

            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Study Material Path</th>
                    <th>Category</th>
                    <th>Topics</th>
                    <th width="100">Status</th>
                    <th width="160">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Name', 'Material path', 'Category', 'Topics', 'Status'] as $label)
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
                @foreach ($courses as $course)
                    <tr>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->study_material_path }}</td>
                        <td>{{ ucfirst($course->category) }}</td>

                        <td class="text-center">
                            @php
                                $count = $course->topics->count();
                                $badgeClass = $count === 0 ? 'bg-danger' : 'bg-success';
                            @endphp
                            <a href="{{ route('courses.topics.index', $course->id) }}" class="text-decoration-none">
                                <span class="badge {{ $badgeClass }} py-2 px-3">
                                    {{ $count }}
                                </span>
                            </a>
                        </td>
                        <td>
                            @if ($course->status === 'inactive')
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
                            <a href="{{ route('courses.topics.index', $course->id) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-list"></i>
                            </a>

                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-info btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this course?')">
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
