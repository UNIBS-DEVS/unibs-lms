@extends('layouts.app')

@section('title', 'Users | Unibs LMS')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Users</h3>

        @if (auth()->user()->role === 'admin')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i>
            </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white w-100" id="listTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th class="text-center">Status</th>
                    <th>Created</th>
                    <th width="13%" class="text-center">Actions</th>
                </tr>

                <tr class="filter-row">
                    @foreach (['Name', 'Email', 'Mobile', 'Role', 'Status', 'Date'] as $label)
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
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->mobile ?? '-' }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td class="text-center">
                            @if ($user->status === 'inactive')
                                <span class="badge bg-danger-subtle text-danger py-2">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> Inactive
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success py-2">
                                    <i class="fa-solid fa-circle-check me-1"></i> Active
                                </span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d M Y H:i a') }}</td>
                        <td class="text-center">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-info btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this user?')">
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
