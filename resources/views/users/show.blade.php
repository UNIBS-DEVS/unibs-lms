@extends('layouts.app')

@section('title', 'View User | Unibs Tools')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-8 col-sm-12">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">User Details</h4>
                <div>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                        <i class="fa fa-edit"></i>
                    </a>

                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </div>
            </div>

            {{-- Card --}}
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Name</div>
                        <div class="col-8">{{ $user->name }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Email</div>
                        <div class="col-8">{{ $user->email }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Mobile</div>
                        <div class="col-8">{{ $user->mobile ?? '-' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Role</div>
                        <div class="col-8">
                            <span class="badge bg-info">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Status</div>
                        <div class="col-8">
                            <span class="badge {{ $user->status === 'inactive' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold">Created</div>
                        <div class="col-8">
                            {{ $user->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
