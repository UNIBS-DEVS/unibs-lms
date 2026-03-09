@extends('layouts.app')

@section('content')
    @include('partials.message')

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Share Feedback</h5>
        </div>

        <form action="{{ route('feedback.share.store') }}" method="POST">
            @csrf

            <div class="card-body">

                <div class="row mb-3">

                    <div class="col-md-4">
                        <label>Batch</label>
                        <select name="batch_id" id="batchSelect" class="form-select" required>
                            <option value="">Select Batch</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if (auth()->user()->role !== 'learner')
                        <div class="col-md-4">
                            <label>Feedback Type</label>
                            <select name="feedback_type" id="feedbackType" class="form-select">
                                <option value="">Select</option>
                                <option value="learner">Learner</option>
                                <option value="trainer">Trainer</option>
                            </select>
                        </div>
                    @endif

                    <div class="col-md-4">
                        <label>Select User</label>
                        <select name="trainer_id" id="userSelect" class="form-select">
                            <option value="">Select</option>
                        </select>
                    </div>

                </div>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th width="200">Rating</th>
                        </tr>
                    </thead>
                    <tbody id="questionsBody"></tbody>
                </table>

                <div class="mt-3">
                    <label>Remarks</label>
                    <textarea name="remarks" id="remarksBox" class="form-control"></textarea>
                </div>

            </div>

            <div class="card-footer text-end">
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const batchSelect = document.getElementById('batchSelect');
            const feedbackType = document.getElementById('feedbackType');
            const userSelect = document.getElementById('userSelect');
            const questionsBody = document.getElementById('questionsBody');

            function getType() {
                @if (auth()->user()->role === 'learner')
                    return 'trainer';
                @else
                    return feedbackType.value;
                @endif
            }

            function loadUsers() {

                if (!batchSelect.value || !getType()) return;

                let url = '';

                if (getType() === 'trainer') {
                    url = `/feedback/share/trainers/${batchSelect.value}`;
                } else {
                    url = `/feedback/share/learners/${batchSelect.value}`;
                }

                fetch(url)
                    .then(res => res.json())
                    .then(data => {

                        userSelect.innerHTML = '<option value="">Select</option>';

                        data.forEach(user => {
                            userSelect.innerHTML += `<option value="${user.id}">${user.name}</option>`;
                        });

                    });
            }

            function loadQuestions() {

                if (!userSelect.value) return;

                fetch(`/feedback/share/questions/${getType()}`)
                    .then(res => res.json())
                    .then(data => {

                        questionsBody.innerHTML = '';

                        data.forEach((q, index) => {

                            let stars = '';

                            for (let i = 1; i <= 5; i++) {
                                stars += `<i class="fa fa-star" data-value="${i}"></i>`;
                            }

                            questionsBody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${q.question}</td>
                            <td>
                                <div class="rating-stars">${stars}</div>
                                <input type="hidden" name="scores[${q.id}]" value="0">
                            </td>
                        </tr>
                    `;
                        });

                        activateStars();
                    });
            }

            function activateStars() {

                document.querySelectorAll('.rating-stars').forEach(container => {

                    const stars = container.querySelectorAll('i');
                    const input = container.nextElementSibling;

                    stars.forEach(star => {

                        star.addEventListener('click', function() {

                            let val = parseInt(star.dataset.value);

                            input.value = val;

                            stars.forEach(s => s.classList.remove('active'));

                            for (let i = 0; i < val; i++) {
                                stars[i].classList.add('active');
                            }
                        });

                    });

                });

            }

            batchSelect.addEventListener('change', loadUsers);

            if (feedbackType) {
                feedbackType.addEventListener('change', loadUsers);
            }

            userSelect.addEventListener('change', loadQuestions);

        });
    </script>

    <style>
        .rating-stars i {
            cursor: pointer;
            color: #ccc;
        }

        .rating-stars i.active {
            color: #f4b400;
        }
    </style>
@endpush
