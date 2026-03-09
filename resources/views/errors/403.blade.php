@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">

                <div class="card shadow-sm border-0 mt-5">
                    <div class="card-body p-5">

                        <i class="fa fa-lock fa-4x text-danger mb-3"></i>

                        <h2 class="fw-bold mb-2">403 – Access Denied</h2>

                        <p class="text-muted mb-4">
                            Sorry, you do not have permission to access this page.
                        </p>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('dashboard.index') }}" class="btn btn-primary">
                                <i class="fa fa-home"></i> Go to Dashboard
                            </a>

                            <button onclick="history.back()" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left"></i> Go Back
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
