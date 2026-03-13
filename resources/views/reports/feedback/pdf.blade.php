<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f2f2f2;
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

    <div class="header">

        <div class="logo">
            <img src="{{ base_path('public/assets/images/company-logo.png') }}" height="60">
        </div>

        <div class="title">
            <h3>Feedback Report</h3>
            <p>{{ now()->format('d M Y') }}</p>
        </div>

        <div class="clearfix"></div>

        <p>
            <strong>Batch :</strong> {{ $batchName ?? 'All Batches' }} |
            <strong>Type :</strong> {{ $feedbackType ? ucfirst($feedbackType) : 'All' }}
        </p>

    </div>

    <table>

        <thead>
            <tr>
                <th>#</th>
                <th>Batch</th>
                <th>Learner</th>
                <th>Trainer</th>
                <th>Type</th>
                <th>Category</th>
                <th>Avg Score</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>

        <tbody>

            @forelse($summaries as $index => $s)
                @php
                    $categories = collect($s['details'] ?? [])
                        ->pluck('category')
                        ->filter()
                        ->unique()
                        ->implode(', ');

                    $avgScore = !empty($s['avg_score']) ? round(($s['avg_score'] / 5) * 100) . '%' : '-';
                @endphp

                <tr>

                    <td>{{ $index + 1 }}</td>

                    <td>{{ $s['batch']['name'] ?? '-' }}</td>

                    <td>{{ $s['learner']['name'] ?? '-' }}</td>

                    <td>{{ $s['trainer']['name'] ?? '-' }}</td>

                    <td>{{ !empty($s['type']) ? ucfirst($s['type']) : '-' }}</td>

                    <td>{{ $categories ?: '-' }}</td>

                    <td>{{ $avgScore ?? '-' }}</td>

                    <td>
                        {{ !empty($s['created_at']) ? \Carbon\Carbon::parse($s['created_at'])->format('d-m-Y H:i') : '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="8">No records found</td>
                </tr>
            @endforelse

        </tbody>

        </tbody>

    </table>

    <div class="footer">
        © {{ date('Y') }} UNI Business Solutions
    </div>

</body>

</html>
