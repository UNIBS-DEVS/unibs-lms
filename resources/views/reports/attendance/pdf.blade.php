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
            text-align: left;
        }

        .present {
            background-color: #28a745;
            /* green */
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .absent {
            background-color: #dc3545;
            /* red */
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .yes {
            background-color: yellow;
            /* red */
            color: #000000;
            font-weight: bold;
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
            <h3>Attendance Report</h3>
            <p>{{ now()->format('d M Y') }}</p>
        </div>

        <div class="clearfix"></div>
    </div>

    <div class="report-info" style="margin-bottom:10px;">
        <strong>Batch :</strong> {{ $batchName ?? 'All Batches' }}
        &nbsp;&nbsp;&nbsp;&nbsp;
        <strong>Course :</strong> {{ $courseName ?? 'All Courses' }}
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>Trainer</th>
                <th>Learner</th>
                <th>Session</th>
                <th>Date</th>
                <th class="text-center">Present / Absent</th>
                <th>LE</th>
                <th>EE</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($attendances as $a)
                <tr>
                    <td>{{ $a['session']['trainer']['name'] ?? '-' }}</td>
                    <td>{{ $a['learner']['name'] ?? '-' }}</td>
                    <td>{{ $a['session']['session_name'] ?? '-' }}</td>

                    <td>
                        {{ !empty($a['session']['start_date'])
                            ? Carbon\Carbon::parse($a['session']['start_date'])->format('d M Y') .
                                ' ' .
                                Carbon\Carbon::parse($a['session']['start_time'])->format('H:i:s')
                            : '-' }}
                    </td>

                    {{-- Present --}}
                    <td style="
                            background-color: {{ $a['present'] === 'present' ? '#28a745' : '#dc3545' }}; color: #ffffff;
                            font-weight: bold;
                        "
                        class="text-center">
                        {{ $a['present'] === 'present' ? 'Present' : 'Absent' }}
                    </td>

                    {{-- Late Entry --}}
                    <td
                        style="
                            background-color: {{ $a['late_entry'] === 'yes' ? '#ffc107' : 'transparent' }};
                            color: {{ $a['late_entry'] === 'yes' ? '#000000' : '#000000' }};
                        ">
                        {{ $a['late_entry'] === 'yes' ? 'Yes' : '-' }}
                    </td>

                    {{-- Early Exit --}}
                    <td
                        style="
                            background-color: {{ $a['early_exit'] === 'yes' ? '#ffc107' : 'transparent' }};
                            color: {{ $a['early_exit'] === 'yes' ? '#000000' : '#000000' }};
                        ">
                        {{ $a['early_exit'] === 'yes' ? 'Yes' : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

    {{-- FOOTER --}}
    <div class="footer">
        © {{ date('Y') }} UNI Business Solutions
    </div>
</body>

</html>
