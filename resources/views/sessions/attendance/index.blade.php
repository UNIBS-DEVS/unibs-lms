@extends('layouts.app')

@push('styles')
    <style>
        .attendance-present {
            border: 1px solid rgb(9, 174, 240);
        }
    </style>
@endpush
@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="mb-1">Attendance</h4>

                <div class="fw-semibold text-primary">
                    {{ $session->session_name }}
                </div>

                <div class="text-muted small mt-1">
                    <i class="fa fa-calendar"></i>
                    {{ \Carbon\Carbon::parse($session->start_date)->format('d M Y') }}
                    —
                    {{ \Carbon\Carbon::parse($session->end_date)->format('d M Y') }}
                </div>

                <div class="text-muted small">
                    <i class="fa fa-clock"></i>
                    {{ $session->start_time }} - {{ $session->end_time }}
                </div>
            </div>

            <!-- SEND EMAIL BUTTON -->
            <form method="POST" action="{{ route('sessions.attendance.email', $session->id) }}">
                @csrf
                <button class="btn btn-outline-primary">
                    <i class="fa fa-envelope"></i> Send Attendance (PDF)
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('sessions.attendance.store', $session->id) }}">
            @csrf

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Learner</th>
                        <th class="text-center">
                            <input type="checkbox" id="checkAllPresent">
                            <div class="small">Present</div>
                        </th>
                        <th class="text-center">Late Entry</th>
                        <th class="text-center">Early Exit</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($learners as $i => $learner)
                        @php
                            $att = $attendance[$learner->id] ?? null;
                        @endphp

                        <tr>
                            <td>{{ $i + 1 }}</td>

                            <td>
                                <div class="fw-semibold">{{ $learner->name }}</div>
                                <div class="text-muted small">{{ $learner->email }}</div>
                            </td>

                            <!-- PRESENT -->
                            <td class="text-center">
                                <input type="checkbox" name="attendance[{{ $learner->id }}][is_present]"
                                    class="form-check-input attendance-present present-checkbox"
                                    {{ $att?->is_present ? 'checked' : '' }} </td>

                                <!-- LATE -->
                            <td class="text-center">
                                <input type="checkbox" name="attendance[{{ $learner->id }}][late_entry]"
                                    class="form-check-input attendance-present" {{ $att?->late_entry ? 'checked' : '' }}>
                            </td>

                            <!-- EARLY EXIT -->
                            <td class="text-center">
                                <input type="checkbox" name="attendance[{{ $learner->id }}][early_exit]"
                                    class="form-check-input attendance-present" {{ $att?->early_exit ? 'checked' : '' }}>
                            </td>

                            <!-- REMARKS -->
                            <td>
                                <input type="text" name="attendance[{{ $learner->id }}][remarks]"
                                    class="form-control form-control-sm" placeholder="Optional"
                                    value="{{ $att?->remarks }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('batch-sessions.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i>
                </a>

                <button class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Attendance
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('checkAllPresent').addEventListener('change', function() {
            const isChecked = this.checked;

            document.querySelectorAll('.present-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
        });
    </script>
@endpush
