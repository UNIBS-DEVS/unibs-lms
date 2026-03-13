@extends('layouts.app')

@section('title', 'Quiz Questions')

@push('styles')
    <style>
        .correct-option {
            background-color: #e9f7ef;
            border-left: 5px solid #28a745;
            font-weight: 600;
        }

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            border-radius: 5px !important;
            padding: 4px 10px;
        }

        .page-item {
            margin-left: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h4>{{ $quiz->title }} – Questions</h4>
                <span class="badge bg-info me-2">
                    Course: {{ $course?->name ?? '-' }}
                </span>

                <span class="badge bg-secondary">
                    Batch: {{ $quiz->batch?->name ?? '-' }}
                </span>
            </div>

            <a href="{{ route('quizzes.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th width="220">Topic</th>
                        <th>Question</th>
                        <th width="160">Type</th>
                        <th width="80" class="text-center">Options</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($questions as $question)
                        <tr>
                            {{-- S.NO --}}
                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $question->topic?->title ?? '-' }}
                            </td>

                            <td>
                                {{ $question->question_text }}
                            </td>

                            <td>
                                {{ str_replace('_', ' ', ucfirst($question->question_type)) }}
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary view-question"
                                    data-question='@json($question)'>
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>

                        </tr>

                        {{-- OPTIONS MODAL --}}
                        <div class="modal fade" id="optionsModal{{ $question->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Options</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        @if ($question->question_type === 'file')
                                            <p class="text-muted">File upload question</p>
                                        @elseif ($question->options->count())
                                            <ul class="list-group">
                                                @foreach ($question->options as $option)
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        {{ $option->option_text }}

                                                        @if ($option->is_correct)
                                                            <span class="badge bg-success">
                                                                Correct
                                                            </span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted">No options available</p>
                                        @endif

                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No questions added
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end ">
                {{ $questions->links() }}
            </div>

        </div>

    </div>

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
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-question');
            if (!btn) return;

            const question = JSON.parse(btn.dataset.question);

            document.getElementById('modal-question').innerText = question.question_text;

            const optionsList = document.getElementById('modal-options');
            optionsList.innerHTML = '';

            if (!question.options || question.options.length === 0) {
                optionsList.innerHTML = `
                <li class="list-group-item text-muted">
                    No options available
                </li>`;
            } else {
                question.options.forEach(option => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item');
                    li.innerText = option.option_text;

                    if (option.is_correct) {
                        li.classList.add('correct-option');
                        li.innerHTML += ' ✅';
                    }

                    optionsList.appendChild(li);
                });
            }
            new bootstrap.Modal(document.getElementById('questionModal')).show();
        });
    </script>
@endpush
