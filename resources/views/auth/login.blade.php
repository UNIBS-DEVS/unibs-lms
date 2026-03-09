@extends('layouts.app')

@section('title', 'Login | Unibs LMS')

@php
    $hideLayout = true;
@endphp

@section('content')

    <div class="card shadow-lg w-100" style="max-width: 370px; border-radius: 12px;">

        <div class="card-header mt-3 text-center bg-transparent border-0">
            <h3 class="text-muted mb-0">Admin Login</h3>
        </div>

        {{-- ✅ MOVE MESSAGE HERE --}}
        <div class="px-4">
            @include('partials.message')
        </div>

        <div class="card-body px-4 py-3">
            <form method="POST" action="{{ route('login.authenticate') }}">
                @csrf

                <!-- Client Code -->
                <div class="mb-3">
                    <label class="form-label">Client Code</label>
                    <input type="text" name="client_code" class="form-control @error('client_code') is-invalid @enderror"
                        placeholder="e.g. UNIBS" value="{{ old('client_code') }}">

                    @error('client_code')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>



                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fa fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="Enter email">
                    </div>

                    @error('email')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fa fa-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter password">
                    </div>

                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                </div>

                <!-- Button -->
                <button type="submit" class="btn btn-primary w-100 btn-login">
                    <i class="fa fa-sign-in-alt me-1"></i> Login
                </button>
            </form>
        </div>

        {{-- <div class="card-footer text-center text-muted bg-transparent border-0">
            © {{ date('Y') }} Unibs LMS
        </div> --}}

        <div class="card-footer text-center bg-transparent border-0">

            {{-- Logo --}}
            <div class="mb-2">
                <img src="{{ asset('assets/images/company-logo.png') }}" alt="Unibs LMS" style="height: 40px;">
            </div>

            <div class="text-muted">
                © {{ date('Y') }} Unibs LMS
            </div>

        </div>

    </div>

@endsection
