@extends('layouts.app')

@section('title', 'Create User | Unibs Tools')

@section('content')
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-user-plus me-2 text-primary"></i>
                            Add User
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            <div class="row g-4">

                                {{-- Name --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="form-control @error('name') is-invalid @enderror">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Mobile --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-phone"></i>
                                        </span>
                                        <input type="text" name="mobile" value="{{ old('mobile') }}"
                                            class="form-control">
                                    </div>
                                </div>

                                {{-- Role --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Role <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-user-tag"></i>
                                        </span>
                                        <select name="role" class="form-select @error('role') is-invalid @enderror">
                                            <option value="">-- None --</option>
                                            @foreach (['admin', 'trainer', 'learner', 'customer'] as $role)
                                                <option value="{{ $role }}" @selected(old('role') === $role)>
                                                    {{ ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-toggle-on"></i>
                                        </span>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">-- None --</option>
                                            <option value="active" @selected(old('status') === 'active')>
                                                Active
                                            </option>
                                            <option value="inactive" @selected(old('status') === 'inactive')>
                                                Inactive
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Confirm Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                        <input type="password" name="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror">
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end mt-3 gap-2">

                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Save
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
