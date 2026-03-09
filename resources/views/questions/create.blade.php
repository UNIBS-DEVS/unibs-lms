@extends('layouts.app')

@section('title', 'Add Question')

@section('content')

    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Add Question</h4>
        </div>

        <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- BASIC DETAILS -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Course -->
                        <div class="col-md-4">
                            <label class="form-label">Course <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Topic -->
                        <div class="col-md-4">
                            <label class="form-label">Topic <span class="text-danger">*</span></label>
                            <select name="topic_id" id="topicSelect" class="form-select" disabled>
                                <option value="">-- None --</option>
                            </select>
                        </div>

                        <!-- Question Type -->
                        <div class="col-md-4">
                            <label class="form-label">Question Type <span class="text-danger">*</span></label>
                            <select name="question_type" id="questionType" class="form-select">
                                <option value="">-- None --</option>
                                <option value="single_choice">Single Choice</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="text">Text</option>
                                <option value="file">File Upload</option>
                            </select>
                        </div>

                        <!-- Question Text -->
                        <div class="col-md-12">
                            <label class="form-label">Question <span class="text-danger">*</span></label>
                            <textarea name="question_text" class="form-control" rows="4" required></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <!-- MCQ SECTION -->
            <div class="card mb-4 d-none" id="mcqSection">
                <div class="card-header">
                    <strong>Options</strong>
                </div>
                <div class="card-body">
                    <div id="optionsWrapper"></div>

                    <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="addOption">
                        <i class="fa fa-plus"></i> Add Option
                    </button>
                </div>
            </div>

            <!-- FILE SECTION -->
            <div class="card mb-4 d-none" id="fileSection">
                <div class="card-header">
                    <strong>File Upload Question</strong>
                </div>
                <div class="card-body">

                    <label class="form-label mt-3">Allowed File Types</label>
                    <input type="text" name="allowed_file_types" class="form-control" placeholder="pdf,docx,jpg,png">

                    <label class="form-label mt-3">Max File Size (MB)</label>
                    <input type="number" name="max_file_size_mb" class="form-control" value="2">

                    <small class="text-muted d-block mt-2">
                        Students will upload their answer file during quiz attempt.
                    </small>

                </div>
            </div>

            <!-- MARKING -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Max Marks <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="max_marks" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Negative Marks</label>
                            <input type="number" step="0.01" name="negative_marks" class="form-control" value="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Marking Type <span class="text-danger">*</span></label>
                            <select name="marking_type" class="form-select" required id="markingType">
                                <option value="automatic">Automatic</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="text-end">
                <a href="{{ route('questions.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>

        </form>
    </div>

@endsection


@push('scripts')
    <script>
        let optionIndex = 0;
        let currentType = null;

        const questionType = document.getElementById('questionType');
        const mcqSection = document.getElementById('mcqSection');
        const fileSection = document.getElementById('fileSection');
        const addOptionBtn = document.getElementById('addOption');
        const markingType = document.getElementById('markingType');
        const optionsWrapper = document.getElementById('optionsWrapper');

        questionType.addEventListener('change', function() {
            currentType = this.value;
            optionIndex = 0;
            optionsWrapper.innerHTML = '';

            mcqSection.classList.add('d-none');
            fileSection.classList.add('d-none');

            if (currentType === 'single_choice' || currentType === 'multiple_choice') {
                markingType.value = 'automatic';
                mcqSection.classList.remove('d-none');
                addOption();
            }

            if (currentType === 'text') {
                markingType.value = 'manual';
            }

            if (currentType === 'file') {
                markingType.value = 'manual';
                fileSection.classList.remove('d-none');
            }
        });

        addOptionBtn.addEventListener('click', addOption);

        function addOption() {
            const isSingle = currentType === 'single_choice';
            const inputType = isSingle ? 'radio' : 'checkbox';

            optionsWrapper.insertAdjacentHTML('beforeend', `
        <div class="row g-2 align-items-center mb-2 option-row">

            <div class="col-md-1 text-center">
                <input type="${inputType}"
                    name="${isSingle ? 'correct_option' : `options[${optionIndex}][is_correct]`}"
                    value="${optionIndex}">
            </div>

            <div class="col-md-8">
                <input type="text"
                    name="options[${optionIndex}][text]"
                    class="form-control"
                    placeholder="Option text"
                    required>
            </div>

            <div class="col-md-2 text-center">
                <button type="button" class="btn btn-sm btn-danger remove-option">
                    <i class="fa fa-trash"></i>
                </button>
            </div>

        </div>
    `);

            optionIndex++;
        }

        optionsWrapper.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-option');
            if (!removeBtn) return;

            removeBtn.closest('.option-row').remove();
        });
    </script>


    <script>
        const courseSelect = document.querySelector('select[name="course_id"]');
        const topicSelect = document.getElementById('topicSelect');

        courseSelect.addEventListener('change', function() {
            const courseId = this.value;

            topicSelect.innerHTML = '<option value="">Loading...</option>';
            topicSelect.disabled = true;

            if (!courseId) {
                topicSelect.innerHTML = '<option value="">-- None --</option>';
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
@endpush
