<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        .center {
            text-align: center;
        }

        .pass {
            background: #28a745;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .fail {
            background: #dc3545;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .logo {
            float: left;
        }

        .title {
            text-align: right;
        }

        .clearfix {
            clear: both;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">

        <div class="logo">
            <img src="{{ public_path('assets/images/company-logo.png') }}" height="60">
        </div>

        <div class="title">
            <h3 style="margin:0;">Quiz Report</h3>
            <p style="margin:3px 0;">{{ now()->format('d M Y') }}</p>
        </div>

        <div class="clearfix"></div>

        <p style="margin-top:10px;">
            <strong>Batch :</strong> {{ $batchName ?? 'All Batches' }} |
            <strong>Quiz :</strong> {{ $quizName ?? 'All Quizzes' }}
        </p>

    </div>

    {{-- TABLE --}}
    <table>

        <thead>
            <tr>
                <th>Trainer</th>
                <th>Learner</th>
                <th>Status</th>
                <th>Score</th>
                <th>Total</th>
                <th>Percentage</th>
                <th>Result</th>
                <th>Started At</th>
                <th>Completed At</th>
            </tr>
        </thead>

        <tbody>

            @forelse($attempts as $a)
                @php

                    $quiz = $a['quiz'] ?? [];
                    $user = $a['user'] ?? [];

                    $trainer = $quiz['batch']['trainers'][0]['name'] ?? '-';

                    $score = $a['score'] ?? 0;

                    $total = collect($quiz['questions'] ?? [])->sum('max_marks');

                    $percentage = $total > 0 ? round(($score / $total) * 100, 2) : null;

                    $passPercent = $quiz['minimum_passing_percentage'] ?? 40;

                    $result = $percentage !== null ? ($percentage >= $passPercent ? 'pass' : 'fail') : null;

                @endphp

                <tr>

                    <td>{{ $trainer }}</td>

                    <td>{{ $user['name'] ?? '-' }}</td>

                    <td class="center">
                        {{ ucfirst(str_replace('_', ' ', $a['status'] ?? '-')) }}
                    </td>

                    <td class="center">
                        {{ $score }}
                    </td>

                    <td class="center">
                        {{ $total ?: '-' }}
                    </td>

                    <td class="center">
                        {{ $percentage !== null ? $percentage . '%' : '-' }}
                    </td>

                    <td class="{{ $result }}">
                        {{ $result ? ucfirst($result) : '-' }}
                    </td>

                    <td class="center">
                        {{ !empty($a['started_at']) ? \Carbon\Carbon::parse($a['started_at'])->format('d-m-Y H:i') : '-' }}
                    </td>

                    <td class="center">
                        {{ !empty($a['completed_at']) ? \Carbon\Carbon::parse($a['completed_at'])->format('d-m-Y H:i') : '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="9" class="center">
                        No records found
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>

    {{-- FOOTER --}}
    <div class="footer">
        © {{ date('Y') }} UNI Business Solutions
    </div>

</body>

</html>
