@extends('layouts.app')

@section('title', 'Edit Quiz')

@section('content')

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa-solid fa-question-circle text-primary"></i>
                Edit Quiz
            </h5>
        </div>

        <div class="card-body">
            @include('quizzes._form', ['quiz' => $quiz])
        </div>
    </div>

@endsection
