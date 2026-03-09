@php
    $isEdit = isset($quiz);
@endphp

<form action="{{ $isEdit ? route('quizzes.update', $quiz) : route('quizzes.store') }}" method="POST">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- ================= BASIC INFO ================= --}}
    <h6 class="text-muted mb-3 border-bottom pb-2">Basic Information</h6>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Quiz Title <span class="text-danger">*</span>
            </label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $quiz->title ?? '') }}" placeholder="Enter quiz title">

            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Batch <span class="text-danger">*</span>
            </label>
            <select name="batch_id" class="form-select @error('batch_id') is-invalid @enderror">
                <option value="">Select Batch</option>
                @foreach ($batches as $id => $name)
                    <option value="{{ $id }}"
                        {{ old('batch_id', $quiz->batch_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>

            @error('batch_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>


    {{-- ================= SETTINGS ================= --}}
    <h6 class="text-muted mb-3 border-bottom pb-2">Quiz Settings</h6>

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <label class="form-label fw-semibold">Quiz Type</label>
            <select name="quiz_type" class="form-select">
                @foreach (['daily', 'weekly', 'monthly', 'need based'] as $type)
                    <option value="{{ $type }}"
                        {{ old('quiz_type', $quiz->quiz_type ?? '') == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Time Limit (Minutes)</label>
            <input type="number" name="time_limit_minutes" class="form-control"
                value="{{ old('time_limit_minutes', $quiz->time_limit_minutes ?? 20) }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Max Attempts</label>
            <input type="number" name="max_attempts" class="form-control"
                value="{{ old('max_attempts', $quiz->max_attempts ?? 1) }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Passing %</label>
            <input type="number" name="minimum_passing_percentage" class="form-control"
                value="{{ old('minimum_passing_percentage', $quiz->minimum_passing_percentage ?? 70) }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Questions Per Page</label>
            <input type="number" name="question_per_page" class="form-control"
                value="{{ old('question_per_page', $quiz->question_per_page ?? 1) }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Difficulty</label>
            <select name="difficulty_level" class="form-select">
                @foreach (['easy', 'medium', 'hard'] as $level)
                    <option value="{{ $level }}"
                        {{ old('difficulty_level', $quiz->difficulty_level ?? 'easy') == $level ? 'selected' : '' }}>
                        {{ ucfirst($level) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}"
                        {{ old('status', $quiz->status ?? 'active') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


    {{-- ================= OPTIONS ================= --}}
    <h6 class="text-muted mb-3 border-bottom pb-2">Options</h6>

    <div class="row mb-4">

        <div class="col-md-4">
            <div class="form-check form-switch">
                <input type="hidden" name="shuffle_questions" value="0">
                <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1"
                    {{ old('shuffle_questions', $quiz->shuffle_questions ?? false) ? 'checked' : '' }}>
                <label class="form-check-label">Shuffle Questions</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-check form-switch">
                <input type="hidden" name="shuffle_options" value="0">
                <input class="form-check-input" type="checkbox" name="shuffle_options" value="1"
                    {{ old('shuffle_options', $quiz->shuffle_options ?? false) ? 'checked' : '' }}>
                <label class="form-check-label">Shuffle Options</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-check form-switch">
                <input type="hidden" name="show_results_immediately" value="0">
                <input class="form-check-input" type="checkbox" name="show_results_immediately" value="1"
                    {{ old('show_results_immediately', $quiz->show_results_immediately ?? false) ? 'checked' : '' }}>
                <label class="form-check-label">Show Results Immediately</label>
            </div>
        </div>

    </div>


    {{-- ================= VISIBILITY ================= --}}
    <h6 class="text-muted mb-3 border-bottom pb-2">Visibility Schedule</h6>

    <div class="row g-3">

        <div class="col-md-3">
            <label class="form-label fw-semibold">Start Date</label>
            <input type="date" name="visible_start_date" class="form-control"
                value="{{ old('visible_start_date', $quiz->visible_start_date ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Start Time</label>
            <input type="time" name="visible_start_time" class="form-control"
                value="{{ old('visible_start_time', $quiz->visible_start_time ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">End Date</label>
            <input type="date" name="visible_end_date" class="form-control"
                value="{{ old('visible_end_date', $quiz->visible_end_date ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">End Time</label>
            <input type="time" name="visible_end_time" class="form-control"
                value="{{ old('visible_end_time', $quiz->visible_end_time ?? '') }}">
        </div>

    </div>

    <div class="d-flex justify-content-end gap-2 pt-3">

        <a href="{{ route('quizzes.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <button type="submit" class="btn btn-{{ $isEdit ? 'warning' : 'primary' }}">
            <i class="fa-solid fa-{{ $isEdit ? 'pen-to-square' : 'floppy-disk' }}"></i>
            {{ $isEdit ? 'Update' : 'Create' }}
        </button>

    </div>

</form>
