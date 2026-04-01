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


                    <div class="col-md-4">

                        @if (auth()->user()->role === 'learner')
                            <label>Select Trainer</label>
                        @else
                            <label>Select Learner</label>
                        @endif

                        @if (auth()->user()->role === 'learner')
                            <select name="trainer_id" id="userSelect" class="form-select">
                                <option value="">-- Select Trainer --</option>
                            </select>
                        @else
                            <select name="learner_id" id="userSelect" class="form-select">
                                <option value="">-- Select Learner --</option>
                            </select>
                        @endif

                    </div>

                </div>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Question</th>
                            <th width="200">Rating</th>
                        </tr>
                    </thead>

                    <tbody id="questionsBody">
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                Please select batch and user
                            </td>
                        </tr>
                    </tbody>

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
            const userSelect = document.getElementById('userSelect');
            const questionsBody = document.getElementById('questionsBody');


            /*
            |--------------------------------------------------------------------------
            | Load Users
            |--------------------------------------------------------------------------
            */

            function loadUsers() {

                if (!batchSelect.value) return;

                let url = '';

                @if (auth()->user()->role === 'learner')
                    url = `/feedback/share/trainers/${batchSelect.value}`;
                @else
                    url = `/feedback/share/learners/${batchSelect.value}`;
                @endif

                fetch(url)
                    .then(res => res.json())
                    .then(data => {

                        userSelect.innerHTML = '<option value="">-- Select --</option>';

                        data.forEach(user => {
                            userSelect.innerHTML += `<option value="${user.id}">${user.name}</option>`;
                        });

                        questionsBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Please select user
                    </td>
                </tr>
            `;

                    });

            }



            /*
            |--------------------------------------------------------------------------
            | Load Questions
            |--------------------------------------------------------------------------
            */

            function loadQuestions() {

                if (!userSelect.value || !batchSelect.value) return;

                fetch(`{{ route('feedback.share.questions') }}?batch_id=${batchSelect.value}`)
                    .then(res => res.json())
                    .then(data => {

                        questionsBody.innerHTML = '';

                        if (data.length === 0) {
                            questionsBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    Please select user
                                </td>
                            </tr>
                        `;
                            return;
                        }

                        data.forEach((q, index) => {

                            let stars = '';

                            for (let i = 1; i <= 5; i++) {
                                stars += `<i class="fa fa-star" data-value="${i}"></i>`;
                            }

                            questionsBody.innerHTML += `
                    <tr>
                        <td>${index+1}</td>
                        <td>${q.question}</td>
                        <td>
                            <div class="rating-stars">${stars}</div>
                            <input type="hidden" name="scores[${q.id}]" value="">
                        </td>
                    </tr>
                `;
                        });

                        activateStars();

                    });

            }



            /*
            |--------------------------------------------------------------------------
            | Star Rating
            |--------------------------------------------------------------------------
            */

            function activateStars() {

                document.querySelectorAll('.rating-stars').forEach(container => {

                    const stars = container.querySelectorAll('i');
                    const input = container.nextElementSibling;

                    stars.forEach((star, index) => {

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



            /*
            |--------------------------------------------------------------------------
            | Events
            |--------------------------------------------------------------------------
            */

            batchSelect.addEventListener('change', loadUsers);

            userSelect.addEventListener('change', loadQuestions);


        });
    </script>



    <style>
        .rating-stars i {
            cursor: pointer;
            color: #ccc;
            font-size: 18px;
            margin-right: 3px;
        }

        .rating-stars i.active {
            color: #f4b400;
        }
    </style>
@endpush
