@extends('layouts.app')

@push('styles')
    <style>
        .already-selected {
            background-color: #e9f7ef;
        }

        .correct-option {
            background-color: #e9f7ef;
            border-left: 5px solid #28a745;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container">

        <div class="mb-4">
            <h4>Add Questions to Quiz</h4>
            <small class="text-muted">Topic-based question selection</small>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Course</label>
                        <input type="text" class="form-control" value="{{ $course->name }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Topic</label>
                        <select class="form-select" id="topic_id">
                            <option value="">Loading topics...</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Search Question</label>
                        <input type="text" class="form-control" id="questionSearch" placeholder="Type to search...">
                    </div>

                </div>
            </div>
        </div>

        <!-- Questions Table -->
        <div class="card">
            <div class="card-body">

                <form id="quizQuestionForm" method="POST" action="{{ route('quizzes.store-questions', $quiz) }}">
                    @csrf

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <div>
                            <span class="badge bg-primary me-2">
                                Selected: <span id="selectedCount">0</span>
                            </span>

                            <select class="form-select d-inline w-auto" id="questionFilter">
                                <option value="all">All</option>
                                <option value="checked">Checked</option>
                                <option value="unchecked">Unchecked</option>
                            </select>
                        </div>

                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Question</th>
                                <th>Type</th>
                                <th width="80">Options</th>
                            </tr>
                        </thead>
                        <tbody id="questions_table">
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Please select a topic
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between mt-3">
                        <div id="pagination_links"></div>

                        <div>
                            <a href="{{ route('quizzes.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="questionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Question Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="modal-question" class="fw-bold mb-3"></p>
                    <ul class="list-group" id="modal-options"></ul>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            const quizQuestionIds = @json($quizQuestionIds);
            const hasAttempts = @json($hasAttempts);

            let selectedQuestionIds = new Set(quizQuestionIds);
            let currentTopic = null;

            updateCount();
            syncHiddenInputs();

            /* ---------------- LOAD TOPICS ---------------- */

            $.get(`/courses/{{ $course->id }}/topics-list`, function(topics) {

                let options = '<option value="">-- Select Topic --</option>';

                $.each(topics, function(i, t) {
                    options += `<option value="${t.id}">${t.title}</option>`;
                });

                $('#topic_id').html(options);

                if (quizQuestionIds.length > 0) {
                    autoSelectTopic();
                }
            });

            function autoSelectTopic() {
                $.get(`/quiz/{{ $quiz->id }}/questions-topics`, function(topicId) {
                    if (topicId) {
                        $('#topic_id').val(topicId);
                        currentTopic = topicId;
                        loadQuestions(topicId, 1);
                    }
                });
            }

            /* ---------------- LOAD QUESTIONS ---------------- */

            function loadQuestions(topicId, page = 1) {

                $.get(`/topics/${topicId}/questions?page=${page}`, function(res) {

                    let tbody = '';
                    let pag = '';

                    if (!res.data.length) {
                        tbody =
                            `<tr><td colspan="4" class="text-center text-muted">No questions found</td></tr>`;
                        $('#questions_table').html(tbody);
                        return;
                    }

                    $.each(res.data, function(i, q) {

                        const checked = selectedQuestionIds.has(q.id);

                        tbody += `
                <tr class="${checked ? 'already-selected' : ''}" data-id="${q.id}">
                    <td>
                        <input type="checkbox"
                            class="row-checkbox"
                            value="${q.id}"
                            ${checked ? 'checked' : ''}>
                    </td>
                    <td class="question-text">${q.question_text}</td>
                    <td>${q.question_type}</td>
                    <td>
                        <button type="button"
                            class="btn btn-sm btn-outline-primary view-question"
                            data-question='${JSON.stringify(q)}'>
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                </tr>`;
                    });

                    $('#questions_table').html(tbody);

                    $.each(res.links, function(i, l) {
                        if (!l.url) return;

                        let pageNum = new URL(l.url).searchParams.get('page');

                        pag += `
                <button type="button"
                    class="btn btn-sm ${l.active ? 'btn-primary':'btn-outline-primary'} mx-1 page-btn"
                    data-page="${pageNum}">
                    ${l.label.replace('&laquo;','«').replace('&raquo;','»')}
                </button>`;
                    });

                    $('#pagination_links').html(pag);

                    updateSelectAllState();
                    applyFilter();
                    applySearch();
                });
            }

            $('#topic_id').change(function() {
                currentTopic = $(this).val();
                if (currentTopic) {
                    loadQuestions(currentTopic, 1);
                }
            });

            $(document).on('click', '.page-btn', function() {
                let page = $(this).data('page');
                loadQuestions(currentTopic, page);
            });

            /* ---------------- CHECK / UNCHECK ---------------- */

            $(document).on('change', '.row-checkbox', function() {

                let id = Number($(this).val());
                let row = $(this).closest('tr');

                if (hasAttempts && !this.checked && quizQuestionIds.includes(id)) {
                    this.checked = true;
                    alert('Cannot remove question. Quiz already has attempts.');
                    return;
                }

                if (this.checked) {
                    selectedQuestionIds.add(id);
                    row.addClass('already-selected');
                } else {
                    selectedQuestionIds.delete(id);
                    row.removeClass('already-selected');
                }

                syncHiddenInputs();
                updateCount();
                updateSelectAllState();
            });

            $('#selectAll').change(function() {

                let isChecked = this.checked;

                $('.row-checkbox:visible').each(function() {

                    let id = Number($(this).val());

                    if (hasAttempts && !isChecked && quizQuestionIds.includes(id)) {
                        return;
                    }

                    $(this).prop('checked', isChecked).trigger('change');
                });
            });

            function updateSelectAllState() {

                let total = $('.row-checkbox:visible').length;
                let checked = $('.row-checkbox:visible:checked').length;

                $('#selectAll').prop('checked', total > 0 && total === checked);
            }

            /* ---------------- FILTER ---------------- */

            $('#questionFilter').change(function() {
                applyFilter();
            });

            function applyFilter() {

                let type = $('#questionFilter').val();

                $('#questions_table tr').each(function() {

                    let checkbox = $(this).find('.row-checkbox');

                    if (!checkbox.length) return;

                    if (type === 'checked') {
                        $(this).toggle(checkbox.is(':checked'));
                    } else if (type === 'unchecked') {
                        $(this).toggle(!checkbox.is(':checked'));
                    } else {
                        $(this).show();
                    }
                });

                updateSelectAllState();
            }

            /* ---------------- SEARCH ---------------- */

            $('#questionSearch').keyup(function() {
                applySearch();
            });

            function applySearch() {

                let term = $('#questionSearch').val().toLowerCase();

                $('#questions_table tr').each(function() {

                    let text = $(this).find('.question-text').text().toLowerCase();

                    if (text.includes(term)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                updateSelectAllState();
            }

            /* ---------------- SYNC ---------------- */

            function syncHiddenInputs() {

                $('.hidden-question').remove();

                selectedQuestionIds.forEach(function(id) {
                    $('#quizQuestionForm').append(
                        `<input type="hidden" name="question_ids[]" value="${id}" class="hidden-question">`
                    );
                });
            }

            function updateCount() {
                $('#selectedCount').text(selectedQuestionIds.size);
            }

            /* ---------------- MODAL ---------------- */

            $(document).on('click', '.view-question', function() {

                let question = $(this).data('question');

                $('#modal-question').text(question.question_text);
                $('#modal-options').html('');

                if (!question.options || !question.options.length) {
                    $('#modal-options').html(
                        '<li class="list-group-item text-muted">No options available</li>');
                } else {
                    $.each(question.options, function(i, o) {

                        let li = $('<li class="list-group-item"></li>').text(o.option_text);

                        if (o.is_correct) {
                            li.addClass('correct-option').append(' ✅');
                        }

                        $('#modal-options').append(li);
                    });
                }

                new bootstrap.Modal($('#questionModal')[0]).show();
            });

        });
    </script>
@endpush
