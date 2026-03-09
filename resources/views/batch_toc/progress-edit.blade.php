@extends('layouts.app')

@section('title', 'Edit Batch TOC | Unibs Tools')

@section('content')
    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit TOC for {{ $batch->name }}</h3>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('progress.update', [$batch->id, $toc->id]) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">Select status</option>
                            @foreach (['planned', 'in_progress', 'on_hold', 'completed'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $toc->status) === $status)>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Actual Start --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Actual Start Date</label>
                        <input type="date" name="actual_start_date"
                            class="form-control @error('actual_start_date') is-invalid @enderror"
                            value="{{ old('actual_start_date', $toc->actual_start_date) }}">
                        @error('actual_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Actual End --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Actual End Date</label>
                        <input type="date" name="actual_end_date"
                            class="form-control @error('actual_end_date') is-invalid @enderror"
                            value="{{ old('actual_end_date', $toc->actual_end_date) }}">
                        @error('actual_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Trainer Remark --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Trainer Remarks</label>
                        <textarea name="remark_trainer" rows="3" class="form-control @error('remark_trainer') is-invalid @enderror">{{ old('remark_trainer', $toc->remark_trainer) }}</textarea>
                        @error('remark_trainer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Progress --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Progress
                            <span class="text-muted ms-1" id="percentageLabel">
                                ({{ old('percentage', $toc->percentage) }}%)
                            </span>
                        </label>

                        <input type="range" name="percentage" id="percentageRange" min="0" max="100"
                            step="1" value="{{ old('percentage', $toc->percentage) }}" class="form-range">

                        <div class="progress mt-2">
                            <div id="percentageBar" class="progress-bar bg-success" role="progressbar"
                                style="width: {{ old('percentage', $toc->percentage) }}%;">
                                {{ old('percentage', $toc->percentage) }}%
                            </div>
                        </div>

                        @error('percentage')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="mt-3 text-end">
                    <a href="{{ route('progress.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>

                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-pen"></i> Update
                    </button>
                </div>

            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const range = document.getElementById('percentageRange');
        const bar = document.getElementById('percentageBar');
        const label = document.getElementById('percentageLabel');

        if (range) {
            range.addEventListener('input', function() {
                bar.style.width = this.value + '%';
                bar.textContent = this.value + '%';
                label.textContent = '(' + this.value + '%)';
            });
        }
    </script>
@endpush
