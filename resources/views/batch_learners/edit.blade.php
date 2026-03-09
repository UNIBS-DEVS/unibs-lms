@extends('layouts.app')

@section('title', 'Edit Batch Learners')

@section('content')

    <div class="container-fluid mt-4">

        @include('partials.message')

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa fa-users me-2 text-warning"></i>
                    Edit Learners – {{ $batch->name }}
                </h5>
            </div>

            <div class="card-body">

                <form action="{{ route('batch-learners.update', $batch->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Batch name --}}
                    <div class="mb-3">
                        <label class="fw-semibold">Batch</label>
                        <input type="text" class="form-control" value="{{ $batch->name }}" disabled>
                    </div>

                    <div class="row align-items-center">

                        {{-- Available learners --}}
                        <div class="col-md-5">
                            <label class="fw-semibold mb-2">Available Learners</label>
                            <select id="available" class="form-select" size="12" multiple>
                                @foreach ($availableLearners as $learner)
                                    <option value="{{ $learner->id }}">
                                        {{ $learner->name }} ({{ $learner->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-md-2 text-center">
                            <button type="button" id="add" class="btn btn-outline-primary mb-2">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                            <br>
                            <button type="button" id="remove" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                        </div>

                        {{-- Assigned learners --}}
                        <div class="col-md-5">
                            <label class="fw-semibold mb-2">Learners in Batch</label>
                            <select id="selected" name="learners[]" class="form-select" size="12" multiple>
                                @foreach ($assignedLearners as $learner)
                                    <option value="{{ $learner->id }}">
                                        {{ $learner->name }} ({{ $learner->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Submit --}}
                    <div class="mt-4 text-end">
                        <a href="{{ route('batch-learners.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </a>

                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-pen me-1"></i>
                            Update
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        $('#add').click(function() {
            $('#available option:selected').appendTo('#selected');
        });

        $('#remove').click(function() {
            $('#selected option:selected').appendTo('#available');
        });

        $('form').submit(function() {
            $('#selected option').prop('selected', true);
        });
    </script>
@endpush
