@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Edit Question</h4>
        </div>

        <form action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Course -->
                        <div class="col-md-4">
                            <label class="form-label">Course *</label>
                            <select name="course_id" class="form-select">
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ $question->course_id == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Topic -->
                        <div class="col-md-4">
                            <label class="form-label">Topic *</label>
                            <select name="topic_id" id="topicSelect" class="form-select">
                                @foreach ($topics->where('course_id', $question->course_id) as $topic)
                                    <option value="{{ $topic->id }}"
                                        {{ $question->topic_id == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Question Type -->
                        <div class="col-md-4">
                            <label class="form-label">Question Type *</label>
                            <select name="question_type" id="questionType" class="form-select">
                                @foreach (['single_choice', 'multiple_choice', 'text', 'file'] as $type)
                                    <option value="{{ $type }}"
                                        {{ $question->question_type === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Question Text -->
                        <div class="col-md-12">
                            <label class="form-label">Question *</label>
                            <textarea name="question_text" class="form-control" rows="4">{{ $question->question_text }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            <!-- MCQ -->
            <div class="card mb-4 d-none" id="mcqSection">
                <div class="card-header"><strong>Options</strong></div>
                <div class="card-body">
                    <div id="optionsWrapper"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addOption">
                        <i class="fa fa-plus"></i> Add Option
                    </button>
                </div>
            </div>

            <!-- FILE -->
            <div class="card mb-4 d-none" id="fileSection">
                <div class="card-body">
                    <label>Allowed File Types</label>
                    <input type="text" name="allowed_file_types" class="form-control"
                        value="{{ old('allowed_file_types', $question->fileSettings?->allowed_file_types) }}">
                    <small class="text-muted">
                        Example: pdf,doc,docx,jpg,png
                    </small><br><br>

                    <label class="mt-2">Max Size (MB)</label>
                    <input type="number" name="max_file_size_mb" class="form-control"
                        value="{{ old('max_file_size_mb', $question->fileSettings->max_file_size_mb ?? 2) }}"
                        min="1">
                </div>
            </div>


            <!-- MARKING -->
            <div class="card mb-4">
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label>Max Marks *</label>
                        <input type="number" name="max_marks" class="form-control" value="{{ $question->max_marks }}"
                            min="0">
                    </div>

                    <div class="col-md-4">
                        <label>Negative Marks</label>
                        <input type="number" name="negative_marks" class="form-control"
                            value="{{ $question->negative_marks }}" min="0">
                    </div>

                    <div class="col-md-4">
                        <label>Marking Type *</label>
                        <select name="marking_type" class="form-select">
                            <option value="automatic" {{ $question->marking_type == 'automatic' ? 'selected' : '' }}>
                                Automatic</option>
                            <option value="manual" {{ $question->marking_type == 'manual' ? 'selected' : '' }}>Manual
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('questions.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-pen"></i> Update
                </button>
            </div>

        </form>
    </div>
@endsection


@push('scripts')
    <script>
        let questionType = document.getElementById('questionType');
        let mcqSection = document.getElementById('mcqSection');
        // let textSection = document.getElementById('textSection');
        let fileSection = document.getElementById('fileSection');
        let optionsWrapper = document.getElementById('optionsWrapper');
        let optionIndex = 0;

        const existingOptions = @json($question->options);

        function toggleSections() {
            mcqSection.classList.add('d-none');
            // textSection.classList.add('d-none');
            fileSection.classList.add('d-none');

            if (['single_choice', 'multiple_choice'].includes(questionType.value)) {
                mcqSection.classList.remove('d-none');
                renderOptions();
            }
            if (questionType.value === 'text') textSection.classList.remove('d-none');
            if (questionType.value === 'file') fileSection.classList.remove('d-none');
        }

        function renderOptions() {
            optionsWrapper.innerHTML = '';
            optionIndex = 0;

            existingOptions.forEach(opt => {
                optionsWrapper.insertAdjacentHTML('beforeend', `
                <div class="row mb-2">
                    <div class="col-md-1 text-center">
                        <input type="${questionType.value === 'single_choice' ? 'radio' : 'checkbox'}"
                               name="${questionType.value === 'single_choice'
                                    ? 'correct_option'
                                    : `options[${optionIndex}][is_correct]`}"
                               value="${optionIndex}"
                               ${opt.is_correct ? 'checked' : ''}>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="options[${optionIndex}][text]"
                               class="form-control" value="${opt.option_text}">
                    </div>
                </div>
            `);
                optionIndex++;
            });
        }

        questionType.addEventListener('change', toggleSections);
        document.addEventListener('DOMContentLoaded', toggleSections);
    </script>

    <script>
        const courseSelect = document.querySelector('select[name="course_id"]');
        const topicSelect = document.getElementById('topicSelect');

        courseSelect.addEventListener('change', function() {
            const courseId = this.value;

            topicSelect.innerHTML = '<option value="">Loading...</option>';
            topicSelect.disabled = true;

            if (!courseId) {
                topicSelect.innerHTML = '<option value="">Select Topic</option>';
                return;
            }

            fetch(`/courses/${courseId}/topics-list`)

                .then(response => response.json())
                .then(data => {
                    topicSelect.innerHTML = '<option value="">-- None --</option>';

                    data.forEach(topic => {
                        topicSelect.innerHTML += `
                    <option value="${topic.id}">${topic.title}</option>
                `;
                    });

                    topicSelect.disabled = false;
                })
                .catch(() => {
                    topicSelect.innerHTML = '<option value="">Error loading topics</option>';
                });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionType = "{{ $question->question_type }}";

            if (questionType === 'file') {
                document.getElementById('fileSection').classList.remove('d-none');
            }
        });
    </script>
@endpush
