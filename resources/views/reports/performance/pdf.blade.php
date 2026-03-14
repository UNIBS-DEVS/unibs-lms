<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        /* Same visual style as your attendance PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            margin-bottom: 15px;
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

        .report-info {
            margin-bottom: 12px;
            font-size: 13px;
        }

        .report-info strong {
            margin-right: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            table-layout: fixed;
            word-wrap: break-word;
        }

        thead th {
            background: #f2f2f2;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: left;
            vertical-align: middle;
        }

        th:nth-child(1),
        td:nth-child(1) {
            width: 4%;
            text-align: center;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 28%;
        }

        th:nth-child(3),
        th:nth-child(4),
        th:nth-child(5),
        th:nth-child(6) {
            width: 12%;
            text-align: center;
        }

        th:nth-child(7),
        td:nth-child(7) {
            width: 12%;
            text-align: center;
        }

        /* status badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 11px;
        }

        .badge-green {
            background-color: #28a745;
            color: #ffffff;
        }

        .badge-yellow {
            background-color: #ffc107;
            color: #000000;
        }

        .badge-red {
            background-color: #dc3545;
            color: #ffffff;
        }

        .footer {
            position: fixed;
            bottom: 18px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        /* responsive small adjustment for long learner names */
        .small {
            font-size: 11px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">
            {{-- adjust path to your logo as needed --}}
            <img src="{{ public_path('assets/images/company-logo.png') }}" height="60" alt="logo">
        </div>

        <div class="title">
            <h3 style="margin:0">Performance Report</h3>
            <p style="margin:2px 0 0 0;">{{ now()->format('d M Y') }}</p>
        </div>

        <div class="clearfix"></div>
    </div>

    {{-- REPORT INFO --}}
    <div class="report-info">
        <strong>Batch :</strong> {{ $batchName ?? 'All Batches' }}

        @if (!empty($startDate) || !empty($endDate))
            &nbsp;&nbsp;&nbsp;&nbsp;
            <strong>Period :</strong>
            {{ $startDate ?? '-' }} — {{ $endDate ?? '-' }}
        @endif
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Learner</th>
                <th>Attendance</th>
                <th>Quiz</th>
                <th>Feedback</th>
                <th>Avg Score</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($performances as $key => $p)
                <tr>
                    <td style="text-align:center">{{ $key + 1 }}</td>

                    <td class="small">
                        {{ $p['learner'] ?? '-' }}
                    </td>

                    <td style="text-align:center">
                        {{ isset($p['attendance']) ? number_format($p['attendance'], 2) . '%' : '-' }}
                    </td>

                    <td style="text-align:center">
                        {{ isset($p['quiz']) ? number_format($p['quiz'], 2) . '%' : '-' }}
                    </td>

                    <td style="text-align:center">
                        {{ isset($p['feedback']) ? number_format($p['feedback'], 2) . '%' : '-' }}
                    </td>

                    <td style="text-align:center; font-weight:700">
                        {{ isset($p['avg_score']) ? number_format($p['avg_score'], 2) . '%' : '-' }}
                    </td>

                    <td style="text-align:center">
                        @php
                            $status = strtolower($p['status'] ?? '');
                        @endphp

                        @if ($status === 'green')
                            <span class="badge badge-green">Green</span>
                        @elseif ($status === 'yellow')
                            <span class="badge badge-yellow">Yellow</span>
                        @elseif ($status === 'red')
                            <span class="badge badge-red">Red</span>
                        @else
                            <span class="badge">{{ $p['status'] ?? '-' }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        © {{ date('Y') }} {{ config('app.name', 'UNI Business Solutions') }}
    </div>

</body>

</html>
