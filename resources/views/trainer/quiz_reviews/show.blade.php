@extends('layouts.app')

@section('content')
    <div class="container">

        <h3 class="mb-3">
            Reviewing: {{ $attempt->quiz->title }}
        </h3>

        <p>
            <strong>Student:</strong> {{ $attempt->user->name }}
        </p>

        <hr>

        @foreach ($answers as $answer)
            <div class="card mb-4">
                <div class="card-body">

                    <h6 class="mb-2">
                        Q{{ $loop->iteration }}.
                        {{ $answer->question->question_text }}
                    </h6>

                    <div class="mb-3">
                        <strong>Student Answer:</strong>

                        @if ($answer->answer_text)
                            <p>{{ $answer->answer_text }}</p>
                        @elseif($answer->answer_file)
                            <a href="{{ route('trainer.quiz-answer.file', $answer->id) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                View File
                            </a>
                        @endif
                    </div>

                    {{-- REVIEW FORM --}}
                    <form method="POST" action="{{ route('trainer.quiz-reviews.answer', $answer) }}" class="review-form">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                <label>Marks (Max: {{ $answer->question->max_marks }})</label>
                                <input type="number" step="0.01" name="marks_obtained"
                                    value="{{ $answer->marks_obtained }}" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label>Correct?</label>
                                <select name="is_correct" class="form-control" required>
                                    <option value="1" @selected($answer->is_correct === true)>Correct</option>
                                    <option value="0" @selected($answer->is_correct === false)>Incorrect</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-success save-btn">
                                    Save Review
                                </button>
                            </div>
                        </div>

                        <small class="save-status fw-bold d-block mt-2"></small>
                    </form>

                </div>
            </div>
        @endforeach

        {{-- PUBLISH RESULT --}}
        <div class="alert alert-info d-flex align-items-center justify-content-between mt-4">
            <div>
                <i class="fa fa-info-circle"></i>
                <strong>Final Step:</strong> Publishing will lock the attempt and show results to learners.
            </div>

            <form method="POST" action="{{ route('trainer.quiz-reviews.publish', $attempt) }}" class="ms-3">
                @csrf
                <button class="btn btn-info btn-lg px-4">
                    Publish Result
                </button>
            </form>
        </div>



    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.review-form').forEach(form => {

                const button = form.querySelector('.save-btn');
                const status = form.querySelector('.save-status');
                const marksInput = form.querySelector('input[name="marks_obtained"]');
                const correctSelect = form.querySelector('select[name="is_correct"]');

                // 🔁 RESET BUTTON WHEN USER EDITS
                function resetButton() {
                    button.disabled = false;
                    button.textContent = 'Save Review';
                    button.className = 'btn btn-success save-btn';
                    status.textContent = '';
                }

                // 🔹 MARKS → INCORRECT
                marksInput.addEventListener('input', function() {
                    const value = parseFloat(this.value) || 0;

                    if (value === 0) {
                        correctSelect.value = "0"; // Incorrect
                    }

                    resetButton();
                });

                // 🔹 INCORRECT → MARKS 0
                correctSelect.addEventListener('change', function() {
                    if (this.value === "0") {
                        marksInput.value = 0;
                    }

                    resetButton();
                });

                // 💾 AJAX SAVE
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    button.disabled = true;
                    button.textContent = 'Saving...';
                    button.className = 'btn btn-secondary save-btn';

                    fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                button.textContent = 'Saved ✓';
                                button.className = 'btn btn-primary save-btn';

                                status.textContent = 'Review saved';
                                status.style.color = 'green';
                            } else {
                                throw data;
                            }
                        })
                        .catch(err => {
                            button.disabled = false;
                            button.textContent = 'Save Failed';
                            button.className = 'btn btn-danger save-btn';

                            status.textContent = err.message || 'Error saving review';
                            status.style.color = 'red';
                        });
                });

            });

        });
    </script>
@endpush
